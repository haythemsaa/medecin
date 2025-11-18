import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Icon from 'react-native-vector-icons/Ionicons';
import { useNavigation } from '@react-navigation/native';
import { useAuthStore } from '../../store/authStore';
import api from '../../services/api';

interface Appointment {
  id: number;
  date_heure: string;
  statut: string;
  motif: string;
  medecin: {
    user: {
      nom: string;
      prenom: string;
    };
    specialite: string;
  };
}

export default function HomeScreen() {
  const navigation = useNavigation();
  const user = useAuthStore((state) => state.user);

  const [upcomingAppointments, setUpcomingAppointments] = useState<Appointment[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadUpcomingAppointments();
  }, []);

  const loadUpcomingAppointments = async () => {
    try {
      const response = await api.getAppointments({ statut: 'confirme', limit: 3 });
      setUpcomingAppointments(response.data);
    } catch (error) {
      console.error('Error loading appointments:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadUpcomingAppointments();
  };

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
      weekday: 'long',
      day: 'numeric',
      month: 'long',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView
        contentContainerStyle={styles.scrollContent}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
      >
        {/* Header */}
        <View style={styles.header}>
          <View>
            <Text style={styles.greeting}>Bonjour,</Text>
            <Text style={styles.userName}>{user?.prenom} {user?.nom}</Text>
          </View>
          <TouchableOpacity style={styles.notificationButton}>
            <Icon name="notifications-outline" size={28} color="#111827" />
            <View style={styles.notificationBadge}>
              <Text style={styles.notificationBadgeText}>3</Text>
            </View>
          </TouchableOpacity>
        </View>

        {/* Quick Actions */}
        <View style={styles.quickActions}>
          <TouchableOpacity
            style={styles.quickActionButton}
            onPress={() => navigation.navigate('Search' as never)}
          >
            <View style={[styles.quickActionIcon, { backgroundColor: '#E0F2FE' }]}>
              <Icon name="search" size={24} color="#0284C7" />
            </View>
            <Text style={styles.quickActionText}>Trouver un médecin</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.quickActionButton}
            onPress={() => navigation.navigate('UrgentConsultation' as never)}
          >
            <View style={[styles.quickActionIcon, { backgroundColor: '#FEE2E2' }]}>
              <Icon name="flash" size={24} color="#DC2626" />
            </View>
            <Text style={styles.quickActionText}>Consultation urgente</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.quickActionButton}
            onPress={() => navigation.navigate('Prescriptions' as never)}
          >
            <View style={[styles.quickActionIcon, { backgroundColor: '#F0FDF4' }]}>
              <Icon name="document-text" size={24} color="#16A34A" />
            </View>
            <Text style={styles.quickActionText}>Mes ordonnances</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.quickActionButton}
            onPress={() => navigation.navigate('MedicationReminders' as never)}
          >
            <View style={[styles.quickActionIcon, { backgroundColor: '#FEF3C7' }]}>
              <Icon name="alarm" size={24} color="#CA8A04" />
            </View>
            <Text style={styles.quickActionText}>Rappels médicaments</Text>
          </TouchableOpacity>
        </View>

        {/* Upcoming Appointments */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <Text style={styles.sectionTitle}>Prochains rendez-vous</Text>
            <TouchableOpacity onPress={() => navigation.navigate('Appointments' as never)}>
              <Text style={styles.seeAllText}>Voir tout</Text>
            </TouchableOpacity>
          </View>

          {loading ? (
            <ActivityIndicator size="large" color="#14B8A6" style={styles.loader} />
          ) : upcomingAppointments.length > 0 ? (
            upcomingAppointments.map((appointment) => (
              <TouchableOpacity
                key={appointment.id}
                style={styles.appointmentCard}
                onPress={() =>
                  navigation.navigate('AppointmentDetails' as never, { id: appointment.id } as never)
                }
              >
                <View style={styles.appointmentIcon}>
                  <Icon name="calendar" size={24} color="#14B8A6" />
                </View>
                <View style={styles.appointmentInfo}>
                  <Text style={styles.appointmentDoctor}>
                    Dr. {appointment.medecin.user.prenom} {appointment.medecin.user.nom}
                  </Text>
                  <Text style={styles.appointmentSpecialty}>{appointment.medecin.specialite}</Text>
                  <Text style={styles.appointmentDate}>{formatDate(appointment.date_heure)}</Text>
                </View>
                <Icon name="chevron-forward" size={20} color="#9CA3AF" />
              </TouchableOpacity>
            ))
          ) : (
            <View style={styles.emptyState}>
              <Icon name="calendar-outline" size={48} color="#9CA3AF" />
              <Text style={styles.emptyStateText}>Aucun rendez-vous à venir</Text>
              <TouchableOpacity
                style={styles.emptyStateButton}
                onPress={() => navigation.navigate('Search' as never)}
              >
                <Text style={styles.emptyStateButtonText}>Prendre un rendez-vous</Text>
              </TouchableOpacity>
            </View>
          )}
        </View>

        {/* Health Tips */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Conseils santé</Text>
          <View style={styles.healthTipCard}>
            <Icon name="water" size={32} color="#14B8A6" />
            <Text style={styles.healthTipText}>
              N'oubliez pas de boire au moins 1.5L d'eau par jour
            </Text>
          </View>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F9FAFB',
  },
  scrollContent: {
    padding: 20,
    paddingBottom: 100,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 24,
  },
  greeting: {
    fontSize: 16,
    color: '#6B7280',
  },
  userName: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#111827',
    marginTop: 4,
  },
  notificationButton: {
    position: 'relative',
  },
  notificationBadge: {
    position: 'absolute',
    top: -4,
    right: -4,
    backgroundColor: '#EF4444',
    borderRadius: 10,
    width: 20,
    height: 20,
    justifyContent: 'center',
    alignItems: 'center',
  },
  notificationBadgeText: {
    color: '#FFFFFF',
    fontSize: 12,
    fontWeight: 'bold',
  },
  quickActions: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
    marginBottom: 24,
  },
  quickActionButton: {
    width: '48%',
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    padding: 16,
    alignItems: 'center',
    marginBottom: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  quickActionIcon: {
    width: 56,
    height: 56,
    borderRadius: 28,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 8,
  },
  quickActionText: {
    fontSize: 13,
    color: '#111827',
    textAlign: 'center',
    fontWeight: '600',
  },
  section: {
    marginBottom: 24,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 16,
  },
  sectionTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#111827',
  },
  seeAllText: {
    fontSize: 14,
    color: '#14B8A6',
    fontWeight: '600',
  },
  loader: {
    marginVertical: 20,
  },
  appointmentCard: {
    flexDirection: 'row',
    alignItems: 'center',
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
  appointmentIcon: {
    width: 48,
    height: 48,
    borderRadius: 24,
    backgroundColor: '#ECFDF5',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  appointmentInfo: {
    flex: 1,
  },
  appointmentDoctor: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#111827',
    marginBottom: 2,
  },
  appointmentSpecialty: {
    fontSize: 14,
    color: '#6B7280',
    marginBottom: 4,
  },
  appointmentDate: {
    fontSize: 13,
    color: '#14B8A6',
  },
  emptyState: {
    alignItems: 'center',
    paddingVertical: 32,
  },
  emptyStateText: {
    fontSize: 16,
    color: '#6B7280',
    marginTop: 12,
    marginBottom: 16,
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
  healthTipCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#ECFDF5',
    borderRadius: 16,
    padding: 16,
  },
  healthTipText: {
    flex: 1,
    fontSize: 14,
    color: '#111827',
    marginLeft: 12,
    lineHeight: 20,
  },
});
