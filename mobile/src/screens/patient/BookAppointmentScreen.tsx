import React, { useState, useEffect } from 'react';
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
import { Calendar } from 'react-native-calendars';
import api from '../../services/api';
import Input from '../../components/Input';
import Button from '../../components/Button';
import { Medecin } from '../../types';

export default function BookAppointmentScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const { doctorId } = route.params as { doctorId: number };

  const [doctor, setDoctor] = useState<Medecin | null>(null);
  const [selectedDate, setSelectedDate] = useState('');
  const [selectedTime, setSelectedTime] = useState('');
  const [consultationType, setConsultationType] = useState<'cabinet' | 'video'>('cabinet');
  const [motif, setMotif] = useState('');
  const [availableSlots, setAvailableSlots] = useState<string[]>([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    loadDoctorInfo();
  }, [doctorId]);

  useEffect(() => {
    if (selectedDate) {
      loadAvailableSlots();
    }
  }, [selectedDate]);

  const loadDoctorInfo = async () => {
    try {
      const response = await api.getDoctorDetails(doctorId);
      setDoctor(response);
    } catch (error) {
      Alert.alert('Erreur', 'Impossible de charger les informations du médecin');
    }
  };

  const loadAvailableSlots = async () => {
    try {
      const response = await api.getDoctorAvailableSlots(doctorId, selectedDate);
      setAvailableSlots(response.slots || []);
    } catch (error) {
      console.error('Error loading slots:', error);
      setAvailableSlots([]);
    }
  };

  const handleBookAppointment = async () => {
    if (!selectedDate || !selectedTime || !motif.trim()) {
      Alert.alert('Erreur', 'Veuillez remplir tous les champs');
      return;
    }

    setLoading(true);
    try {
      const dateTime = `${selectedDate} ${selectedTime}`;
      await api.createAppointment({
        medecin_id: doctorId,
        date_heure: dateTime,
        type_consultation: consultationType,
        motif: motif.trim(),
      });

      Alert.alert(
        'Succès',
        'Votre rendez-vous a été créé avec succès',
        [
          {
            text: 'OK',
            onPress: () => navigation.navigate('Appointments' as never),
          },
        ]
      );
    } catch (error: any) {
      Alert.alert(
        'Erreur',
        error.response?.data?.message || 'Impossible de créer le rendez-vous'
      );
    } finally {
      setLoading(false);
    }
  };

  const getMarkedDates = () => {
    if (!selectedDate) return {};
    return {
      [selectedDate]: {
        selected: true,
        selectedColor: '#14B8A6',
      },
    };
  };

  return (
    <SafeAreaView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Icon name="arrow-back" size={24} color="#111827" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Prendre rendez-vous</Text>
        <View style={{ width: 24 }} />
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent}>
        {/* Doctor Info */}
        {doctor && (
          <View style={styles.doctorInfo}>
            <Text style={styles.doctorName}>
              Dr. {doctor.user.prenom} {doctor.user.nom}
            </Text>
            <Text style={styles.doctorSpecialty}>{doctor.specialite}</Text>
            <Text style={styles.doctorTarif}>{doctor.tarif} TND</Text>
          </View>
        )}

        {/* Consultation Type */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Type de consultation</Text>
          <View style={styles.typeContainer}>
            <TouchableOpacity
              style={[
                styles.typeButton,
                consultationType === 'cabinet' && styles.typeButtonActive,
              ]}
              onPress={() => setConsultationType('cabinet')}
            >
              <Icon
                name="business"
                size={24}
                color={consultationType === 'cabinet' ? '#14B8A6' : '#6B7280'}
              />
              <Text
                style={[
                  styles.typeButtonText,
                  consultationType === 'cabinet' && styles.typeButtonTextActive,
                ]}
              >
                Au cabinet
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[
                styles.typeButton,
                consultationType === 'video' && styles.typeButtonActive,
              ]}
              onPress={() => setConsultationType('video')}
            >
              <Icon
                name="videocam"
                size={24}
                color={consultationType === 'video' ? '#14B8A6' : '#6B7280'}
              />
              <Text
                style={[
                  styles.typeButtonText,
                  consultationType === 'video' && styles.typeButtonTextActive,
                ]}
              >
                Vidéo consultation
              </Text>
            </TouchableOpacity>
          </View>
        </View>

        {/* Calendar */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Sélectionner une date</Text>
          <Calendar
            minDate={new Date().toISOString().split('T')[0]}
            markedDates={getMarkedDates()}
            onDayPress={(day) => {
              setSelectedDate(day.dateString);
              setSelectedTime('');
            }}
            theme={{
              selectedDayBackgroundColor: '#14B8A6',
              todayTextColor: '#14B8A6',
              arrowColor: '#14B8A6',
            }}
          />
        </View>

        {/* Time Slots */}
        {selectedDate && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Sélectionner une heure</Text>
            {availableSlots.length > 0 ? (
              <View style={styles.slotsContainer}>
                {availableSlots.map((slot) => (
                  <TouchableOpacity
                    key={slot}
                    style={[
                      styles.slotButton,
                      selectedTime === slot && styles.slotButtonActive,
                    ]}
                    onPress={() => setSelectedTime(slot)}
                  >
                    <Text
                      style={[
                        styles.slotButtonText,
                        selectedTime === slot && styles.slotButtonTextActive,
                      ]}
                    >
                      {slot}
                    </Text>
                  </TouchableOpacity>
                ))}
              </View>
            ) : (
              <Text style={styles.noSlotsText}>
                Aucun créneau disponible pour cette date
              </Text>
            )}
          </View>
        )}

        {/* Motif */}
        <View style={styles.section}>
          <Input
            label="Motif de consultation *"
            placeholder="Décrivez brièvement le motif de votre consultation"
            value={motif}
            onChangeText={setMotif}
            multiline
            numberOfLines={4}
            textAlignVertical="top"
          />
        </View>

        {/* Book Button */}
        <View style={styles.section}>
          <Button
            title="Confirmer le rendez-vous"
            onPress={handleBookAppointment}
            loading={loading}
            disabled={!selectedDate || !selectedTime || !motif.trim()}
          />
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
  headerTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#111827',
  },
  scrollContent: {
    paddingBottom: 32,
  },
  doctorInfo: {
    backgroundColor: '#FFFFFF',
    padding: 20,
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
  },
  doctorName: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#111827',
    marginBottom: 4,
  },
  doctorSpecialty: {
    fontSize: 14,
    color: '#6B7280',
    marginBottom: 8,
  },
  doctorTarif: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#14B8A6',
  },
  section: {
    backgroundColor: '#FFFFFF',
    padding: 20,
    marginTop: 12,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#111827',
    marginBottom: 16,
  },
  typeContainer: {
    flexDirection: 'row',
    gap: 12,
  },
  typeButton: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    padding: 16,
    borderRadius: 12,
    borderWidth: 2,
    borderColor: '#E5E7EB',
    backgroundColor: '#F9FAFB',
  },
  typeButtonActive: {
    borderColor: '#14B8A6',
    backgroundColor: '#ECFDF5',
  },
  typeButtonText: {
    fontSize: 14,
    color: '#6B7280',
    marginLeft: 8,
    fontWeight: '600',
  },
  typeButtonTextActive: {
    color: '#14B8A6',
  },
  slotsContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
  },
  slotButton: {
    paddingVertical: 12,
    paddingHorizontal: 16,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: '#E5E7EB',
    backgroundColor: '#F9FAFB',
  },
  slotButtonActive: {
    borderColor: '#14B8A6',
    backgroundColor: '#14B8A6',
  },
  slotButtonText: {
    fontSize: 14,
    color: '#374151',
    fontWeight: '600',
  },
  slotButtonTextActive: {
    color: '#FFFFFF',
  },
  noSlotsText: {
    fontSize: 14,
    color: '#6B7280',
    textAlign: 'center',
    fontStyle: 'italic',
  },
});
