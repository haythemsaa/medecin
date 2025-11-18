import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  ActivityIndicator,
  RefreshControl,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Icon from 'react-native-vector-icons/Ionicons';
import { useNavigation } from '@react-navigation/native';
import api from '../../services/api';

interface Appointment {
  id: number;
  date_heure: string;
  statut: string;
  motif: string;
  type_consultation: string;
  medecin: {
    user: {
      nom: string;
      prenom: string;
    };
    specialite: string;
  };
}

const STATUS_TABS = [
  { key: 'all', label: 'Tous', statut: undefined },
  { key: 'confirme', label: 'Confirmés', statut: 'confirme' },
  { key: 'en_attente', label: 'En attente', statut: 'en_attente' },
  { key: 'termine', label: 'Terminés', statut: 'termine' },
  { key: 'annule', label: 'Annulés', statut: 'annule' },
];

export default function AppointmentsScreen() {
  const navigation = useNavigation();

  const [appointments, setAppointments] = useState<Appointment[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [selectedStatus, setSelectedStatus] = useState('all');

  useEffect(() => {
    loadAppointments();
  }, [selectedStatus]);

  const loadAppointments = async () => {
    try {
      const statusFilter = STATUS_TABS.find((tab) => tab.key === selectedStatus)?.statut;
      const response = await api.getAppointments(
        statusFilter ? { statut: statusFilter } : {}
      );
      setAppointments(response.data);
    } catch (error) {
      console.error('Error loading appointments:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadAppointments();
  };

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
      weekday: 'short',
      day: 'numeric',
      month: 'short',
      year: 'numeric',
    });
  };

  const formatTime = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('fr-FR', {
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  const getStatusColor = (statut: string) => {
    switch (statut) {
      case 'confirme':
        return '#10B981';
      case 'en_attente':
        return '#F59E0B';
      case 'termine':
        return '#6B7280';
      case 'annule':
        return '#EF4444';
      default:
        return '#9CA3AF';
    }
  };

  const getStatusLabel = (statut: string) => {
    switch (statut) {
      case 'confirme':
        return 'Confirmé';
      case 'en_attente':
        return 'En attente';
      case 'termine':
        return 'Terminé';
      case 'annule':
        return 'Annulé';
      default:
        return statut;
    }
  };

  const renderAppointmentCard = ({ item }: { item: Appointment }) => (
    <TouchableOpacity
      style={styles.appointmentCard}
      onPress={() => navigation.navigate('AppointmentDetails' as never, { id: item.id } as never)}
    >
      <View style={styles.appointmentHeader}>
        <View style={styles.appointmentDate}>
          <Icon name="calendar-outline" size={16} color="#6B7280" />
          <Text style={styles.appointmentDateText}>{formatDate(item.date_heure)}</Text>
        </View>
        <View style={[styles.statusBadge, { backgroundColor: getStatusColor(item.statut) + '20' }]}>
          <Text style={[styles.statusBadgeText, { color: getStatusColor(item.statut) }]}>
            {getStatusLabel(item.statut)}
          </Text>
        </View>
      </View>

      <View style={styles.appointmentBody}>
        <View style={styles.doctorInfo}>
          <Text style={styles.doctorName}>
            Dr. {item.medecin.user.prenom} {item.medecin.user.nom}
          </Text>
          <Text style={styles.doctorSpecialty}>{item.medecin.specialite}</Text>
        </View>

        <View style={styles.appointmentDetails}>
          <View style={styles.detailRow}>
            <Icon name="time-outline" size={16} color="#6B7280" />
            <Text style={styles.detailText}>{formatTime(item.date_heure)}</Text>
          </View>
          <View style={styles.detailRow}>
            <Icon
              name={item.type_consultation === 'cabinet' ? 'business-outline' : 'videocam-outline'}
              size={16}
              color="#6B7280"
            />
            <Text style={styles.detailText}>
              {item.type_consultation === 'cabinet' ? 'Au cabinet' : 'Vidéo consultation'}
            </Text>
          </View>
        </View>

        {item.motif && (
          <Text style={styles.appointmentMotif} numberOfLines={2}>
            {item.motif}
          </Text>
        )}
      </View>

      <View style={styles.appointmentFooter}>
        <TouchableOpacity style={styles.actionButton}>
          <Icon name="information-circle-outline" size={20} color="#14B8A6" />
          <Text style={styles.actionButtonText}>Détails</Text>
        </TouchableOpacity>

        {item.statut === 'confirme' && item.type_consultation === 'video' && (
          <TouchableOpacity style={[styles.actionButton, styles.primaryActionButton]}>
            <Icon name="videocam" size={20} color="#FFFFFF" />
            <Text style={[styles.actionButtonText, styles.primaryActionButtonText]}>
              Rejoindre
            </Text>
          </TouchableOpacity>
        )}
      </View>
    </TouchableOpacity>
  );

  return (
    <SafeAreaView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <Text style={styles.title}>Mes rendez-vous</Text>
        <TouchableOpacity onPress={() => navigation.navigate('Search' as never)}>
          <Icon name="add-circle" size={32} color="#14B8A6" />
        </TouchableOpacity>
      </View>

      {/* Status Tabs */}
      <View style={styles.tabsContainer}>
        <FlatList
          horizontal
          showsHorizontalScrollIndicator={false}
          data={STATUS_TABS}
          keyExtractor={(item) => item.key}
          renderItem={({ item }) => (
            <TouchableOpacity
              style={[styles.tab, selectedStatus === item.key && styles.tabActive]}
              onPress={() => setSelectedStatus(item.key)}
            >
              <Text
                style={[styles.tabText, selectedStatus === item.key && styles.tabTextActive]}
              >
                {item.label}
              </Text>
            </TouchableOpacity>
          )}
        />
      </View>

      {/* Appointments List */}
      {loading ? (
        <ActivityIndicator size="large" color="#14B8A6" style={styles.loader} />
      ) : (
        <FlatList
          data={appointments}
          renderItem={renderAppointmentCard}
          keyExtractor={(item) => item.id.toString()}
          contentContainerStyle={styles.listContent}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
          ListEmptyComponent={
            <View style={styles.emptyState}>
              <Icon name="calendar-outline" size={64} color="#9CA3AF" />
              <Text style={styles.emptyStateText}>Aucun rendez-vous</Text>
              <TouchableOpacity
                style={styles.emptyStateButton}
                onPress={() => navigation.navigate('Search' as never)}
              >
                <Text style={styles.emptyStateButtonText}>Prendre un rendez-vous</Text>
              </TouchableOpacity>
            </View>
          }
        />
      )}
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F9FAFB',
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 16,
    backgroundColor: '#FFFFFF',
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#111827',
  },
  tabsContainer: {
    backgroundColor: '#FFFFFF',
    paddingVertical: 12,
    paddingHorizontal: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
  },
  tab: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
    marginRight: 8,
    backgroundColor: '#F3F4F6',
  },
  tabActive: {
    backgroundColor: '#14B8A6',
  },
  tabText: {
    fontSize: 14,
    color: '#6B7280',
    fontWeight: '600',
  },
  tabTextActive: {
    color: '#FFFFFF',
  },
  loader: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  listContent: {
    padding: 16,
    paddingBottom: 100,
  },
  appointmentCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    padding: 16,
    marginBottom: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  appointmentHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  appointmentDate: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  appointmentDateText: {
    fontSize: 14,
    color: '#6B7280',
    marginLeft: 6,
    fontWeight: '600',
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusBadgeText: {
    fontSize: 12,
    fontWeight: 'bold',
  },
  appointmentBody: {
    marginBottom: 12,
  },
  doctorInfo: {
    marginBottom: 8,
  },
  doctorName: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#111827',
    marginBottom: 2,
  },
  doctorSpecialty: {
    fontSize: 14,
    color: '#6B7280',
  },
  appointmentDetails: {
    flexDirection: 'row',
    gap: 16,
    marginBottom: 8,
  },
  detailRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  detailText: {
    fontSize: 13,
    color: '#6B7280',
    marginLeft: 6,
  },
  appointmentMotif: {
    fontSize: 14,
    color: '#111827',
    fontStyle: 'italic',
  },
  appointmentFooter: {
    flexDirection: 'row',
    justifyContent: 'flex-end',
    gap: 8,
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: '#F3F4F6',
  },
  actionButton: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 8,
    backgroundColor: '#F3F4F6',
  },
  actionButtonText: {
    fontSize: 14,
    color: '#14B8A6',
    fontWeight: '600',
    marginLeft: 4,
  },
  primaryActionButton: {
    backgroundColor: '#14B8A6',
  },
  primaryActionButtonText: {
    color: '#FFFFFF',
  },
  emptyState: {
    alignItems: 'center',
    paddingVertical: 64,
  },
  emptyStateText: {
    fontSize: 18,
    fontWeight: '600',
    color: '#6B7280',
    marginTop: 16,
    marginBottom: 24,
  },
  emptyStateButton: {
    backgroundColor: '#14B8A6',
    borderRadius: 12,
    paddingVertical: 12,
    paddingHorizontal: 24,
  },
  emptyStateButtonText: {
    color: '#FFFFFF',
    fontSize: 14,
    fontWeight: 'bold',
  },
});
