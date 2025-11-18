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

interface Stats {
  today_appointments: number;
  pending_appointments: number;
  total_patients: number;
  urgent_requests: number;
}

interface Appointment {
  id: number;
  date_heure: string;
  statut: string;
  type_consultation: string;
  patient: {
    user: {
      nom: string;
      prenom: string;
    };
  };
}

export default function DoctorDashboardScreen() {
  const navigation = useNavigation();
  const user = useAuthStore((state) => state.user);

  const [stats, setStats] = useState<Stats | null>(null);
  const [todayAppointments, setTodayAppointments] = useState<Appointment[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadDashboardData();
  }, []);

  const loadDashboardData = async () => {
    try {
      // Load stats
      const statsResponse = await api.getDoctorStats();
      setStats(statsResponse);

      // Load today's appointments
      const appointmentsResponse = await api.getDoctorAppointments({
        date: new Date().toISOString().split('T')[0],
      });
      setTodayAppointments(appointmentsResponse.data);
    } catch (error) {
      console.error('Error loading dashboard data:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadDashboardData();
  };

  const formatTime = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('fr-FR', {
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
            <Text style={styles.greeting}>Bonjour Dr.,</Text>
            <Text style={styles.userName}>{user?.prenom} {user?.nom}</Text>
          </View>
          <TouchableOpacity style={styles.notificationButton}>
            <Icon name="notifications-outline" size={28} color="#111827" />
            {stats && stats.pending_appointments > 0 && (
              <View style={styles.notificationBadge}>
                <Text style={styles.notificationBadgeText}>{stats.pending_appointments}</Text>
              </View>
            )}
          </TouchableOpacity>
        </View>

        {/* Stats Cards */}
        {loading ? (
          <ActivityIndicator size="large" color="#14B8A6" style={styles.loader} />
        ) : stats && (
          <View style={styles.statsContainer}>
            <View style={[styles.statCard, { backgroundColor: '#E0F2FE' }]}>
              <Icon name="calendar-outline" size={32} color="#0284C7" />
              <Text style={styles.statNumber}>{stats.today_appointments}</Text>
              <Text style={styles.statLabel}>RDV aujourd'hui</Text>
            </View>

            <View style={[styles.statCard, { backgroundColor: '#FEF3C7' }]}>
              <Icon name="time-outline" size={32} color="#CA8A04" />
              <Text style={styles.statNumber}>{stats.pending_appointments}</Text>
              <Text style={styles.statLabel}>En attente</Text>
            </View>

            <View style={[styles.statCard, { backgroundColor: '#F0FDF4' }]}>
              <Icon name="people-outline" size={32} color="#16A34A" />
              <Text style={styles.statNumber}>{stats.total_patients}</Text>
              <Text style={styles.statLabel}>Patients</Text>
            </View>

            <View style={[styles.statCard, { backgroundColor: '#FEE2E2' }]}>
              <Icon name="flash-outline" size={32} color="#DC2626" />
              <Text style={styles.statNumber}>{stats.urgent_requests}</Text>
              <Text style={styles.statLabel}>Urgences</Text>
            </View>
          </View>
        )}

        {/* Quick Actions */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Actions rapides</Text>
          <View style={styles.quickActions}>
            <TouchableOpacity
              style={styles.quickActionButton}
              onPress={() => navigation.navigate('DoctorAppointments' as never)}
            >
              <Icon name="calendar" size={24} color="#FFFFFF" />
              <Text style={styles.quickActionText}>Mes RDV</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={styles.quickActionButton}
              onPress={() => navigation.navigate('DoctorSchedule' as never)}
            >
              <Icon name="time" size={24} color="#FFFFFF" />
              <Text style={styles.quickActionText}>Planning</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={styles.quickActionButton}
              onPress={() => navigation.navigate('DoctorPatients' as never)}
            >
              <Icon name="people" size={24} color="#FFFFFF" />
              <Text style={styles.quickActionText}>Patients</Text>
            </TouchableOpacity>
          </View>
        </View>

        {/* Today's Appointments */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <Text style={styles.sectionTitle}>Rendez-vous aujourd'hui</Text>
            <TouchableOpacity onPress={() => navigation.navigate('DoctorAppointments' as never)}>
              <Text style={styles.seeAllText}>Voir tout</Text>
            </TouchableOpacity>
          </View>

          {todayAppointments.length > 0 ? (
            todayAppointments.map((appointment) => (
              <TouchableOpacity
                key={appointment.id}
                style={styles.appointmentCard}
                onPress={() =>
                  navigation.navigate('DoctorAppointmentDetails' as never, { id: appointment.id } as never)
                }
              >
                <View style={styles.appointmentTime}>
                  <Icon name="time" size={20} color="#14B8A6" />
                  <Text style={styles.appointmentTimeText}>{formatTime(appointment.date_heure)}</Text>
                </View>
                <View style={styles.appointmentInfo}>
                  <Text style={styles.appointmentPatient}>
                    {appointment.patient.user.prenom} {appointment.patient.user.nom}
                  </Text>
                  <Text style={styles.appointmentType}>
                    {appointment.type_consultation === 'cabinet' ? 'Au cabinet' : 'Vidéo'}
                  </Text>
                </View>
                <Icon name="chevron-forward" size={20} color="#9CA3AF" />
              </TouchableOpacity>
            ))
          ) : (
            <View style={styles.emptyState}>
              <Icon name="calendar-outline" size={48} color="#9CA3AF" />
              <Text style={styles.emptyStateText}>Aucun rendez-vous aujourd'hui</Text>
            </View>
          )}
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
  loader: {
    marginVertical: 40,
  },
  statsContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
    marginBottom: 24,
  },
  statCard: {
    width: '48%',
    borderRadius: 16,
    padding: 16,
    alignItems: 'center',
    marginBottom: 12,
  },
  statNumber: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#111827',
    marginTop: 8,
  },
  statLabel: {
    fontSize: 13,
    color: '#6B7280',
    marginTop: 4,
    textAlign: 'center',
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
  quickActions: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  quickActionButton: {
    flex: 1,
    backgroundColor: '#14B8A6',
    borderRadius: 12,
    padding: 16,
    alignItems: 'center',
    marginHorizontal: 4,
  },
  quickActionText: {
    color: '#FFFFFF',
    fontSize: 14,
    fontWeight: 'bold',
    marginTop: 8,
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
  appointmentTime: {
    flexDirection: 'row',
    alignItems: 'center',
    marginRight: 12,
  },
  appointmentTimeText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#14B8A6',
    marginLeft: 6,
  },
  appointmentInfo: {
    flex: 1,
  },
  appointmentPatient: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#111827',
    marginBottom: 2,
  },
  appointmentType: {
    fontSize: 14,
    color: '#6B7280',
  },
  emptyState: {
    alignItems: 'center',
    paddingVertical: 32,
  },
  emptyStateText: {
    fontSize: 16,
    color: '#6B7280',
    marginTop: 12,
  },
});
