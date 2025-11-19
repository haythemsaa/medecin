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
import { Prescription } from '../../types';
import EmptyState from '../../components/EmptyState';
import LoadingSpinner from '../../components/LoadingSpinner';

export default function PrescriptionsScreen() {
  const navigation = useNavigation();

  const [prescriptions, setPrescriptions] = useState<Prescription[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadPrescriptions();
  }, []);

  const loadPrescriptions = async () => {
    try {
      const response = await api.getPrescriptions();
      setPrescriptions(response.data || []);
    } catch (error) {
      console.error('Error loading prescriptions:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadPrescriptions();
  };

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    });
  };

  const renderPrescriptionCard = ({ item }: { item: Prescription }) => (
    <TouchableOpacity
      style={styles.prescriptionCard}
      onPress={() =>
        navigation.navigate('PrescriptionDetails' as never, { id: item.id } as never)
      }
    >
      <View style={styles.cardHeader}>
        <View style={styles.cardHeaderLeft}>
          <Icon name="document-text" size={24} color="#14B8A6" />
          <View style={styles.cardHeaderInfo}>
            <Text style={styles.doctorName}>
              Dr. {item.medecin?.user.prenom} {item.medecin?.user.nom}
            </Text>
            <Text style={styles.specialty}>{item.medecin?.specialite}</Text>
          </View>
        </View>
        {item.renewable && (
          <View style={styles.renewableBadge}>
            <Icon name="reload" size={14} color="#10B981" />
            <Text style={styles.renewableText}>Renouvelable</Text>
          </View>
        )}
      </View>

      <View style={styles.cardBody}>
        <View style={styles.infoRow}>
          <Icon name="calendar-outline" size={16} color="#6B7280" />
          <Text style={styles.infoText}>{formatDate(item.date_prescription)}</Text>
        </View>
        <View style={styles.infoRow}>
          <Icon name="medical-outline" size={16} color="#6B7280" />
          <Text style={styles.infoText}>
            {item.medicaments.length} médicament{item.medicaments.length > 1 ? 's' : ''}
          </Text>
        </View>
        {item.duree_traitement && (
          <View style={styles.infoRow}>
            <Icon name="time-outline" size={16} color="#6B7280" />
            <Text style={styles.infoText}>Durée: {item.duree_traitement}</Text>
          </View>
        )}
      </View>

      <View style={styles.cardFooter}>
        <TouchableOpacity style={styles.actionButton}>
          <Icon name="eye-outline" size={20} color="#14B8A6" />
          <Text style={styles.actionButtonText}>Voir détails</Text>
        </TouchableOpacity>
        {item.renewable && (
          <TouchableOpacity
            style={[styles.actionButton, styles.renewButton]}
            onPress={() =>
              navigation.navigate('PrescriptionRenewal' as never, {
                prescriptionId: item.id,
              } as never)
            }
          >
            <Icon name="reload" size={20} color="#FFFFFF" />
            <Text style={[styles.actionButtonText, styles.renewButtonText]}>
              Renouveler
            </Text>
          </TouchableOpacity>
        )}
      </View>
    </TouchableOpacity>
  );

  if (loading) {
    return <LoadingSpinner />;
  }

  return (
    <SafeAreaView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Icon name="arrow-back" size={24} color="#111827" />
        </TouchableOpacity>
        <Text style={styles.title}>Mes ordonnances</Text>
        <View style={{ width: 24 }} />
      </View>

      <FlatList
        data={prescriptions}
        renderItem={renderPrescriptionCard}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={styles.listContent}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        ListEmptyComponent={
          <EmptyState
            icon="document-text-outline"
            title="Aucune ordonnance"
            subtitle="Vos ordonnances apparaîtront ici après vos consultations"
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
  listContent: {
    padding: 16,
    paddingBottom: 100,
  },
  prescriptionCard: {
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
    alignItems: 'center',
    marginBottom: 12,
  },
  cardHeaderLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  cardHeaderInfo: {
    marginLeft: 12,
    flex: 1,
  },
  doctorName: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#111827',
    marginBottom: 2,
  },
  specialty: {
    fontSize: 13,
    color: '#6B7280',
  },
  renewableBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#D1FAE5',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  renewableText: {
    fontSize: 11,
    color: '#10B981',
    fontWeight: 'bold',
    marginLeft: 4,
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
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 8,
    backgroundColor: '#F3F4F6',
  },
  actionButtonText: {
    fontSize: 13,
    color: '#14B8A6',
    fontWeight: '600',
    marginLeft: 4,
  },
  renewButton: {
    backgroundColor: '#14B8A6',
  },
  renewButtonText: {
    color: '#FFFFFF',
  },
});
