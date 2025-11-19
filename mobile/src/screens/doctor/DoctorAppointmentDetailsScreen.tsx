import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Alert,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Icon from 'react-native-vector-icons/Ionicons';
import { useNavigation, useRoute } from '@react-navigation/native';
import api from '../../services/api';
import { Appointment } from '../../types';
import { formatDate, formatTime } from '../../utils/format';
import Button from '../../components/Button';
import LoadingSpinner from '../../components/LoadingSpinner';

export default function DoctorAppointmentDetailsScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const { id } = route.params as { id: number };

  const [appointment, setAppointment] = useState<Appointment | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadAppointmentDetails();
  }, [id]);

  const loadAppointmentDetails = async () => {
    try {
      const response = await api.getDoctorAppointmentDetails(id);
      setAppointment(response);
    } catch (error) {
      console.error('Error loading appointment details:', error);
      Alert.alert('Erreur', 'Impossible de charger les détails du rendez-vous');
    } finally {
      setLoading(false);
    }
  };

  const handleStartConsultation = () => {
    navigation.navigate('DoctorVideoConsultation' as never, { appointmentId: id } as never);
  };

  const handleCreatePrescription = () => {
    navigation.navigate('CreatePrescription' as never, { appointmentId: id } as never);
  };

  const handleCompleteAppointment = async () => {
    Alert.alert(
      'Terminer la consultation',
      'Êtes-vous sûr de vouloir marquer cette consultation comme terminée ?',
      [
        { text: 'Annuler', style: 'cancel' },
        {
          text: 'Terminer',
          onPress: async () => {
            try {
              await api.updateAppointment(id, { statut: 'termine' });
              Alert.alert('Succès', 'Consultation terminée', [
                { text: 'OK', onPress: () => navigation.goBack() },
              ]);
            } catch (error) {
              Alert.alert('Erreur', 'Impossible de terminer la consultation');
            }
          },
        },
      ]
    );
  };

  if (loading) {
    return <LoadingSpinner />;
  }

  if (!appointment) {
    return (
      <View style={styles.errorContainer}>
        <Text style={styles.errorText}>Rendez-vous introuvable</Text>
      </View>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Icon name="arrow-back" size={24} color="#111827" />
        </TouchableOpacity>
        <Text style={styles.title}>Détails du rendez-vous</Text>
        <View style={{ width: 24 }} />
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent}>
        {/* Patient Info */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Informations patient</Text>
          <View style={styles.patientCard}>
            <View style={styles.patientInfo}>
              <Text style={styles.patientName}>
                {appointment.patient?.user.prenom} {appointment.patient?.user.nom}
              </Text>
              <View style={styles.infoRow}>
                <Icon name="call-outline" size={16} color="#6B7280" />
                <Text style={styles.infoText}>{appointment.patient?.user.telephone}</Text>
              </View>
              <View style={styles.infoRow}>
                <Icon name="mail-outline" size={16} color="#6B7280" />
                <Text style={styles.infoText}>{appointment.patient?.user.email}</Text>
              </View>
              {appointment.patient?.numero_securite_sociale && (
                <View style={styles.infoRow}>
                  <Icon name="card-outline" size={16} color="#6B7280" />
                  <Text style={styles.infoText}>
                    Sécurité sociale: {appointment.patient.numero_securite_sociale}
                  </Text>
                </View>
              )}
            </View>
          </View>
        </View>

        {/* Appointment Details */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Détails de la consultation</Text>
          <View style={styles.detailsCard}>
            <View style={styles.detailRow}>
              <Icon name="calendar-outline" size={20} color="#14B8A6" />
              <View style={styles.detailInfo}>
                <Text style={styles.detailLabel}>Date</Text>
                <Text style={styles.detailValue}>{formatDate(appointment.date_heure, 'long')}</Text>
              </View>
            </View>

            <View style={styles.detailRow}>
              <Icon name="time-outline" size={20} color="#14B8A6" />
              <View style={styles.detailInfo}>
                <Text style={styles.detailLabel}>Heure</Text>
                <Text style={styles.detailValue}>{formatTime(appointment.date_heure)}</Text>
              </View>
            </View>

            <View style={styles.detailRow}>
              <Icon
                name={appointment.type_consultation === 'cabinet' ? 'business-outline' : 'videocam-outline'}
                size={20}
                color="#14B8A6"
              />
              <View style={styles.detailInfo}>
                <Text style={styles.detailLabel}>Type</Text>
                <Text style={styles.detailValue}>
                  {appointment.type_consultation === 'cabinet' ? 'Au cabinet' : 'Vidéo consultation'}
                </Text>
              </View>
            </View>

            {appointment.motif && (
              <View style={styles.motifContainer}>
                <Text style={styles.motifLabel}>Motif de consultation:</Text>
                <Text style={styles.motifText}>{appointment.motif}</Text>
              </View>
            )}
          </View>
        </View>

        {/* Medical History (if available) */}
        {appointment.patient?.antecedents_medicaux && appointment.patient.antecedents_medicaux.length > 0 && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Antécédents médicaux</Text>
            <View style={styles.historyCard}>
              {appointment.patient.antecedents_medicaux.map((antecedent, index) => (
                <View key={index} style={styles.historyItem}>
                  <Icon name="medical-outline" size={16} color="#6B7280" />
                  <Text style={styles.historyText}>{antecedent}</Text>
                </View>
              ))}
            </View>
          </View>
        )}

        {/* Allergies (if available) */}
        {appointment.patient?.allergies && appointment.patient.allergies.length > 0 && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Allergies</Text>
            <View style={styles.allergiesCard}>
              {appointment.patient.allergies.map((allergie, index) => (
                <View key={index} style={styles.allergyChip}>
                  <Icon name="warning-outline" size={14} color="#DC2626" />
                  <Text style={styles.allergyText}>{allergie}</Text>
                </View>
              ))}
            </View>
          </View>
        )}

        {/* Actions */}
        <View style={styles.actionsSection}>
          {appointment.type_consultation === 'video' && appointment.statut === 'confirme' && (
            <Button
              title="Démarrer la consultation vidéo"
              onPress={handleStartConsultation}
              icon={<Icon name="videocam" size={20} color="#FFFFFF" />}
            />
          )}

          {appointment.statut === 'confirme' && (
            <Button
              title="Créer une ordonnance"
              onPress={handleCreatePrescription}
              variant="outline"
              icon={<Icon name="document-text-outline" size={20} color="#14B8A6" />}
              style={{ marginTop: 12 }}
            />
          )}

          {appointment.statut === 'confirme' && (
            <Button
              title="Marquer comme terminé"
              onPress={handleCompleteAppointment}
              variant="secondary"
              icon={<Icon name="checkmark-circle-outline" size={20} color="#FFFFFF" />}
              style={{ marginTop: 12 }}
            />
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
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  errorText: {
    fontSize: 16,
    color: '#6B7280',
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
  scrollContent: {
    paddingBottom: 32,
  },
  section: {
    backgroundColor: '#FFFFFF',
    padding: 20,
    marginTop: 12,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#111827',
    marginBottom: 16,
  },
  patientCard: {
    backgroundColor: '#F9FAFB',
    borderRadius: 12,
    padding: 16,
  },
  patientInfo: {},
  patientName: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#111827',
    marginBottom: 12,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8,
  },
  infoText: {
    fontSize: 14,
    color: '#374151',
    marginLeft: 8,
  },
  detailsCard: {
    backgroundColor: '#F9FAFB',
    borderRadius: 12,
    padding: 16,
  },
  detailRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 16,
  },
  detailInfo: {
    marginLeft: 12,
    flex: 1,
  },
  detailLabel: {
    fontSize: 12,
    color: '#6B7280',
    marginBottom: 2,
  },
  detailValue: {
    fontSize: 16,
    fontWeight: '600',
    color: '#111827',
  },
  motifContainer: {
    marginTop: 8,
    padding: 12,
    backgroundColor: '#FFFFFF',
    borderRadius: 8,
  },
  motifLabel: {
    fontSize: 12,
    fontWeight: 'bold',
    color: '#6B7280',
    marginBottom: 6,
  },
  motifText: {
    fontSize: 14,
    color: '#111827',
    lineHeight: 20,
  },
  historyCard: {
    backgroundColor: '#F9FAFB',
    borderRadius: 12,
    padding: 16,
  },
  historyItem: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginBottom: 8,
  },
  historyText: {
    fontSize: 14,
    color: '#374151',
    marginLeft: 8,
    flex: 1,
  },
  allergiesCard: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
  },
  allergyChip: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FEE2E2',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 16,
  },
  allergyText: {
    fontSize: 12,
    color: '#DC2626',
    fontWeight: '600',
    marginLeft: 4,
  },
  actionsSection: {
    padding: 20,
  },
});
