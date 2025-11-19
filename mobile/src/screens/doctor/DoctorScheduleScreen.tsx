import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Switch,
  Alert,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Icon from 'react-native-vector-icons/Ionicons';
import { useNavigation } from '@react-navigation/native';
import api from '../../services/api';
import { Availability } from '../../types';
import Button from '../../components/Button';
import LoadingSpinner from '../../components/LoadingSpinner';

const DAYS_OF_WEEK = [
  { id: 1, name: 'Lundi' },
  { id: 2, name: 'Mardi' },
  { id: 3, name: 'Mercredi' },
  { id: 4, name: 'Jeudi' },
  { id: 5, name: 'Vendredi' },
  { id: 6, name: 'Samedi' },
  { id: 0, name: 'Dimanche' },
];

const TIME_SLOTS = [
  '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
  '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30',
  '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30',
];

export default function DoctorScheduleScreen() {
  const navigation = useNavigation();

  const [availabilities, setAvailabilities] = useState<Availability[]>([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    loadSchedule();
  }, []);

  const loadSchedule = async () => {
    try {
      const response = await api.getDoctorAvailability();
      setAvailabilities(response.data || []);
    } catch (error) {
      console.error('Error loading schedule:', error);
    } finally {
      setLoading(false);
    }
  };

  const toggleDayAvailability = (dayId: number) => {
    const existingAvailability = availabilities.find((a) => a.day_of_week === dayId);

    if (existingAvailability) {
      // Update existing
      const updated = availabilities.map((a) =>
        a.day_of_week === dayId ? { ...a, is_available: !a.is_available } : a
      );
      setAvailabilities(updated);
    } else {
      // Add new with default times
      setAvailabilities([
        ...availabilities,
        {
          id: Date.now(), // Temporary ID
          medecin_id: 0, // Will be set by API
          day_of_week: dayId,
          start_time: '09:00',
          end_time: '17:00',
          is_available: true,
        },
      ]);
    }
  };

  const updateTime = (dayId: number, field: 'start_time' | 'end_time', value: string) => {
    const updated = availabilities.map((a) =>
      a.day_of_week === dayId ? { ...a, [field]: value } : a
    );
    setAvailabilities(updated);
  };

  const handleSaveSchedule = async () => {
    setSaving(true);
    try {
      await api.updateDoctorAvailability(availabilities);
      Alert.alert('Succès', 'Votre planning a été mis à jour');
    } catch (error: any) {
      Alert.alert('Erreur', error.response?.data?.message || 'Impossible de mettre à jour le planning');
    } finally {
      setSaving(false);
    }
  };

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
        <Text style={styles.title}>Mon planning</Text>
        <View style={{ width: 24 }} />
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent}>
        <View style={styles.infoCard}>
          <Icon name="information-circle" size={24} color="#14B8A6" />
          <Text style={styles.infoText}>
            Configurez vos horaires de disponibilité. Les patients ne pourront prendre rendez-vous que pendant ces créneaux.
          </Text>
        </View>

        {DAYS_OF_WEEK.map((day) => {
          const availability = availabilities.find((a) => a.day_of_week === day.id);
          const isAvailable = availability?.is_available || false;

          return (
            <View key={day.id} style={styles.dayCard}>
              <View style={styles.dayHeader}>
                <Text style={styles.dayName}>{day.name}</Text>
                <Switch
                  value={isAvailable}
                  onValueChange={() => toggleDayAvailability(day.id)}
                  trackColor={{ false: '#D1D5DB', true: '#9FE2BF' }}
                  thumbColor={isAvailable ? '#14B8A6' : '#F3F4F6'}
                />
              </View>

              {isAvailable && availability && (
                <View style={styles.timeContainer}>
                  <View style={styles.timeRow}>
                    <Text style={styles.timeLabel}>Début:</Text>
                    <View style={styles.timePickerContainer}>
                      <Icon name="time-outline" size={20} color="#6B7280" />
                      <select
                        value={availability.start_time}
                        onChange={(e) => updateTime(day.id, 'start_time', e.target.value)}
                        style={styles.timePicker as any}
                      >
                        {TIME_SLOTS.map((time) => (
                          <option key={time} value={time}>
                            {time}
                          </option>
                        ))}
                      </select>
                    </View>
                  </View>

                  <View style={styles.timeRow}>
                    <Text style={styles.timeLabel}>Fin:</Text>
                    <View style={styles.timePickerContainer}>
                      <Icon name="time-outline" size={20} color="#6B7280" />
                      <select
                        value={availability.end_time}
                        onChange={(e) => updateTime(day.id, 'end_time', e.target.value)}
                        style={styles.timePicker as any}
                      >
                        {TIME_SLOTS.map((time) => (
                          <option key={time} value={time}>
                            {time}
                          </option>
                        ))}
                      </select>
                    </View>
                  </View>

                  <Text style={styles.durationText}>
                    Durée: {calculateDuration(availability.start_time, availability.end_time)}
                  </Text>
                </View>
              )}
            </View>
          );
        })}

        <View style={styles.saveButtonContainer}>
          <Button
            title="Enregistrer le planning"
            onPress={handleSaveSchedule}
            loading={saving}
            icon={<Icon name="save-outline" size={20} color="#FFFFFF" />}
          />
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const calculateDuration = (start: string, end: string): string => {
  const [startHour, startMin] = start.split(':').map(Number);
  const [endHour, endMin] = end.split(':').map(Number);

  const startMinutes = startHour * 60 + startMin;
  const endMinutes = endHour * 60 + endMin;
  const duration = endMinutes - startMinutes;

  const hours = Math.floor(duration / 60);
  const minutes = duration % 60;

  if (hours > 0 && minutes > 0) {
    return `${hours}h${minutes}`;
  } else if (hours > 0) {
    return `${hours}h`;
  } else {
    return `${minutes}min`;
  }
};

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
    padding: 16,
    paddingBottom: 32,
  },
  infoCard: {
    flexDirection: 'row',
    backgroundColor: '#ECFDF5',
    borderRadius: 12,
    padding: 16,
    marginBottom: 16,
  },
  infoText: {
    flex: 1,
    fontSize: 14,
    color: '#047857',
    lineHeight: 20,
    marginLeft: 12,
  },
  dayCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  dayHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  dayName: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#111827',
  },
  timeContainer: {
    marginTop: 16,
    paddingTop: 16,
    borderTopWidth: 1,
    borderTopColor: '#F3F4F6',
  },
  timeRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  timeLabel: {
    fontSize: 14,
    color: '#6B7280',
    width: 60,
  },
  timePickerContainer: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#F9FAFB',
    borderRadius: 8,
    padding: 12,
  },
  timePicker: {
    flex: 1,
    marginLeft: 8,
    fontSize: 16,
    color: '#111827',
    border: 'none',
    backgroundColor: 'transparent',
  },
  durationText: {
    fontSize: 12,
    color: '#14B8A6',
    fontWeight: '600',
    textAlign: 'right',
  },
  saveButtonContainer: {
    marginTop: 16,
  },
});
