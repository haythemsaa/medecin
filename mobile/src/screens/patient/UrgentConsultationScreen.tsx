import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Alert,
  RefreshControl,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Icon from 'react-native-vector-icons/Ionicons';
import { useNavigation } from '@react-navigation/native';
import api from '../../services/api';
import { Medecin } from '../../types';
import Input from '../../components/Input';
import Button from '../../components/Button';
import DoctorCard from '../../components/DoctorCard';
import EmptyState from '../../components/EmptyState';

export default function UrgentConsultationScreen() {
  const navigation = useNavigation();

  const [availableDoctors, setAvailableDoctors] = useState<Medecin[]>([]);
  const [selectedDoctor, setSelectedDoctor] = useState<Medecin | null>(null);
  const [motif, setMotif] = useState('');
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    loadAvailableDoctors();
  }, []);

  const loadAvailableDoctors = async () => {
    try {
      const response = await api.getUrgentAvailableDoctors();
      setAvailableDoctors(response.doctors || []);
    } catch (error) {
      console.error('Error loading urgent doctors:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadAvailableDoctors();
  };

  const calculateUrgentFee = (baseTarif: number) => {
    return baseTarif * 1.5; // 50% surcharge for urgent consultations
  };

  const handleRequestUrgentConsultation = async () => {
    if (!selectedDoctor) {
      Alert.alert('Erreur', 'Veuillez sélectionner un médecin');
      return;
    }

    if (!motif.trim()) {
      Alert.alert('Erreur', 'Veuillez décrire le motif de votre consultation urgente');
      return;
    }

    setSubmitting(true);
    try {
      await api.requestUrgentConsultation({
        medecin_id: selectedDoctor.id,
        motif: motif.trim(),
      });

      Alert.alert(
        'Demande envoyée',
        'Votre demande de consultation urgente a été envoyée au médecin. Vous recevrez une notification dès qu\'il aura répondu.',
        [
          {
            text: 'OK',
            onPress: () => navigation.navigate('Home' as never),
          },
        ]
      );
    } catch (error: any) {
      Alert.alert(
        'Erreur',
        error.response?.data?.message || 'Impossible d\'envoyer la demande'
      );
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Icon name="arrow-back" size={24} color="#111827" />
        </TouchableOpacity>
        <Text style={styles.title}>Consultation urgente</Text>
        <View style={{ width: 24 }} />
      </View>

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
      >
        {/* Info Banner */}
        <View style={styles.infoBanner}>
          <Icon name="flash" size={24} color="#DC2626" />
          <View style={styles.infoTextContainer}>
            <Text style={styles.infoTitle}>Consultation dans l'heure</Text>
            <Text style={styles.infoSubtitle}>
              Les médecins disponibles peuvent vous consulter dans l'heure qui suit
            </Text>
            <Text style={styles.infoFee}>
              Tarif urgent: +50% du tarif normal
            </Text>
          </View>
        </View>

        {/* Motif */}
        <View style={styles.section}>
          <Input
            label="Motif de la consultation urgente *"
            placeholder="Décrivez votre problème de santé actuel"
            value={motif}
            onChangeText={setMotif}
            multiline
            numberOfLines={4}
            textAlignVertical="top"
            icon="create-outline"
          />
        </View>

        {/* Available Doctors */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>
            Médecins disponibles ({availableDoctors.length})
          </Text>

          {availableDoctors.length > 0 ? (
            availableDoctors.map((doctor) => (
              <TouchableOpacity
                key={doctor.id}
                onPress={() => setSelectedDoctor(doctor)}
              >
                <View
                  style={[
                    styles.doctorCardContainer,
                    selectedDoctor?.id === doctor.id && styles.doctorCardSelected,
                  ]}
                >
                  <DoctorCard
                    doctor={doctor}
                    onPress={() => setSelectedDoctor(doctor)}
                  />
                  {selectedDoctor?.id === doctor.id && (
                    <View style={styles.selectedBadge}>
                      <Icon name="checkmark-circle" size={24} color="#10B981" />
                    </View>
                  )}
                  <View style={styles.urgentFeeContainer}>
                    <Text style={styles.urgentFeeLabel}>Tarif urgent:</Text>
                    <Text style={styles.urgentFeeValue}>
                      {calculateUrgentFee(doctor.tarif)} TND
                    </Text>
                  </View>
                </View>
              </TouchableOpacity>
            ))
          ) : (
            <EmptyState
              icon="flash-outline"
              title="Aucun médecin disponible"
              subtitle="Aucun médecin n'est disponible pour une consultation urgente pour le moment. Réessayez plus tard ou prenez un rendez-vous normal."
              action={
                <Button
                  title="Rechercher un médecin"
                  variant="outline"
                  onPress={() => navigation.navigate('Search' as never)}
                />
              }
            />
          )}
        </View>

        {/* Submit Button */}
        {availableDoctors.length > 0 && (
          <View style={styles.section}>
            <Button
              title={`Demander une consultation urgente${
                selectedDoctor
                  ? ` - ${calculateUrgentFee(selectedDoctor.tarif)} TND`
                  : ''
              }`}
              onPress={handleRequestUrgentConsultation}
              loading={submitting}
              disabled={!selectedDoctor || !motif.trim()}
              icon={<Icon name="flash" size={20} color="#FFFFFF" />}
            />
          </View>
        )}
      </ScrollView>
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
  scrollContent: {
    paddingBottom: 32,
  },
  infoBanner: {
    flexDirection: 'row',
    backgroundColor: '#FEE2E2',
    padding: 16,
    marginTop: 12,
    marginHorizontal: 16,
    borderRadius: 12,
  },
  infoTextContainer: {
    flex: 1,
    marginLeft: 12,
  },
  infoTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#991B1B',
    marginBottom: 4,
  },
  infoSubtitle: {
    fontSize: 13,
    color: '#7F1D1D',
    lineHeight: 18,
    marginBottom: 4,
  },
  infoFee: {
    fontSize: 12,
    color: '#DC2626',
    fontWeight: '600',
  },
  section: {
    backgroundColor: '#FFFFFF',
    padding: 16,
    marginTop: 12,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#111827',
    marginBottom: 16,
  },
  doctorCardContainer: {
    position: 'relative',
    borderRadius: 16,
    borderWidth: 2,
    borderColor: 'transparent',
    marginBottom: 12,
  },
  doctorCardSelected: {
    borderColor: '#10B981',
  },
  selectedBadge: {
    position: 'absolute',
    top: 8,
    right: 8,
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.2,
    shadowRadius: 2,
    elevation: 2,
  },
  urgentFeeContainer: {
    flexDirection: 'row',
    justifyContent: 'flex-end',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingBottom: 12,
    backgroundColor: '#FEF3C7',
    borderBottomLeftRadius: 14,
    borderBottomRightRadius: 14,
    marginTop: -12,
  },
  urgentFeeLabel: {
    fontSize: 13,
    color: '#92400E',
    marginRight: 8,
  },
  urgentFeeValue: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#B45309',
  },
});
