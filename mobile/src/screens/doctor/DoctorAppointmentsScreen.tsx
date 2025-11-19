import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  RefreshControl,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Icon from 'react-native-vector-icons/Ionicons';
import { useNavigation } from '@react-navigation/native';
import api from '../../services/api';
import { Appointment } from '../../types';
import { formatDate, formatTime } from '../../utils/format';
import EmptyState from '../../components/EmptyState';
import Button from '../../components/Button';

const STATUS_FILTERS = [
  { key: 'all', label: 'Tous' },
  { key: 'en_attente', label: 'En attente' },
  { key: 'confirme', label: 'Confirmés' },
  { key: 'termine', label: 'Terminés' },
];

export default function DoctorAppointmentsScreen() {
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
      const params = selectedStatus !== 'all' ? { statut: selectedStatus } : {};
      const response = await api.getDoctorAppointments(params);
      setAppointments(response.data || []);
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

  const handleAcceptAppointment = async (appointmentId: number) => {
    try {
      await api.updateAppointment(appointmentId, { statut: 'confirme' });
      loadAppointments();
    } catch (error) {
      console.error('Error accepting appointment:', error);
    }
  };

  const handleRejectAppointment = async (appointmentId: number) => {
    try {
      await api.updateAppointment(appointmentId, { statut: 'annule' });
      loadAppointments();
    } catch (error) {
      console.error('Error rejecting appointment:', error);
    }
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

  const renderAppointmentCard = ({ item }: { item: Appointment }) => (
    <View style={styles.appointmentCard}>
      <View style={styles.cardHeader}>
        <View style={styles.patientInfo}>
          <Text style={styles.patientName}>
            {item.patient?.user.prenom} {item.patient?.user.nom}
          </Text>
          <Text style={styles.patientPhone}>{item.patient?.user.telephone}</Text>
        </View>
        <View style={[styles.statusBadge, { backgroundColor: getStatusColor(item.statut) + '20' }]}>
          <Text style={[styles.statusText, { color: getStatusColor(item.statut) }]}>
            {item.statut}
          </Text>
        </View>
      </View>

      <View style={styles.cardBody}>
        <View style={styles.infoRow}>
          <Icon name="calendar-outline" size={16} color="#6B7280" />
          <Text style={styles.infoText}>{formatDate(item.date_heure, 'long')}</Text>
        </View>
        <View style={styles.infoRow}>
          <Icon name="time-outline" size={16} color="#6B7280" />
          <Text style={styles.infoText}>{formatTime(item.date_heure)}</Text>
        </View>
        <View style={styles.infoRow}>
          <Icon
            name={item.type_consultation === 'cabinet' ? 'business-outline' : 'videocam-outline'}
            size={16}
            color="#6B7280"
          />
          <Text style={styles.infoText}>
            {item.type_consultation === 'cabinet' ? 'Au cabinet' : 'Vidéo consultation'}
          </Text>
        </View>
        {item.motif && (
          <View style={styles.motifContainer}>
            <Text style={styles.motifLabel}>Motif:</Text>
            <Text style={styles.motifText}>{item.motif}</Text>
          </View>
        )}
      </View>

      <View style={styles.cardFooter}>
        {item.statut === 'en_attente' && (
          <>
            <TouchableOpacity
              style={[styles.actionButton, styles.rejectButton]}
              onPress={() => handleRejectAppointment(item.id)}
            >
              <Icon name="close" size={20} color="#EF4444" />
              <Text style={[styles.actionButtonText, styles.rejectText]}>Refuser</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[styles.actionButton, styles.acceptButton]}
              onPress={() => handleAcceptAppointment(item.id)}
            >
              <Icon name="checkmark" size={20} color="#FFFFFF" />
              <Text style={[styles.actionButtonText, styles.acceptText]}>Accepter</Text>
            </TouchableOpacity>
          </>
        )}
        {item.statut === 'confirme' && (
          <TouchableOpacity
            style={[styles.actionButton, styles.viewButton]}
            onPress={() =>
              navigation.navigate('DoctorAppointmentDetails' as never, { id: item.id } as never)
            }
          >
            <Icon name="eye-outline" size={20} color="#14B8A6" />
            <Text style={[styles.actionButtonText, styles.viewText]}>Voir détails</Text>
          </TouchableOpacity>
        )}
      </View>
    </View>
  );

  return (
    <SafeAreaView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Icon name="arrow-back" size={24} color="#111827" />
        </TouchableOpacity>
        <Text style={styles.title}>Mes rendez-vous</Text>
        <View style={{ width: 24 }} />
      </View>

      {/* Filters */}
      <View style={styles.filtersContainer}>
        <FlatList
          horizontal
          showsHorizontalScrollIndicator={false}
          data={STATUS_FILTERS}
          keyExtractor={(item) => item.key}
          renderItem={({ item }) => (
            <TouchableOpacity
              style={[styles.filterChip, selectedStatus === item.key && styles.filterChipActive]}
              onPress={() => setSelectedStatus(item.key)}
            >
              <Text
                style={[
                  styles.filterChipText,
                  selectedStatus === item.key && styles.filterChipTextActive,
                ]}
              >
                {item.label}
              </Text>
            </TouchableOpacity>
          )}
        />
      </View>

      {/* Appointments List */}
      <FlatList
        data={appointments}
        renderItem={renderAppointmentCard}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={styles.listContent}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        ListEmptyComponent={
          <EmptyState
            icon="calendar-outline"
            title="Aucun rendez-vous"
            subtitle="Les rendez-vous apparaîtront ici"
          />
        }
      />
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
    fontSize: 20,
    fontWeight: 'bold',
    color: '#111827',
  },
  filtersContainer: {
    backgroundColor: '#FFFFFF',
    paddingVertical: 12,
    paddingHorizontal: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
  },
  filterChip: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
    marginRight: 8,
    backgroundColor: '#F3F4F6',
  },
  filterChipActive: {
    backgroundColor: '#14B8A6',
  },
  filterChipText: {
    fontSize: 14,
    color: '#6B7280',
    fontWeight: '600',
  },
  filterChipTextActive: {
    color: '#FFFFFF',
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
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 12,
  },
  patientInfo: {
    flex: 1,
  },
  patientName: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#111827',
    marginBottom: 4,
  },
  patientPhone: {
    fontSize: 14,
    color: '#6B7280',
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    fontSize: 12,
    fontWeight: 'bold',
  },
  cardBody: {
    marginBottom: 12,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 6,
  },
  infoText: {
    fontSize: 14,
    color: '#374151',
    marginLeft: 8,
  },
  motifContainer: {
    marginTop: 8,
    padding: 12,
    backgroundColor: '#F9FAFB',
    borderRadius: 8,
  },
  motifLabel: {
    fontSize: 12,
    fontWeight: 'bold',
    color: '#6B7280',
    marginBottom: 4,
  },
  motifText: {
    fontSize: 14,
    color: '#111827',
    lineHeight: 20,
  },
  cardFooter: {
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
  },
  rejectButton: {
    backgroundColor: '#FEE2E2',
  },
  acceptButton: {
    backgroundColor: '#14B8A6',
  },
  viewButton: {
    backgroundColor: '#F3F4F6',
  },
  actionButtonText: {
    fontSize: 14,
    fontWeight: '600',
    marginLeft: 4,
  },
  rejectText: {
    color: '#EF4444',
  },
  acceptText: {
    color: '#FFFFFF',
  },
  viewText: {
    color: '#14B8A6',
  },
});
