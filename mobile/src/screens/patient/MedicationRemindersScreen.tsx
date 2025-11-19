import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  RefreshControl,
  Switch,
  Alert,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Icon from 'react-native-vector-icons/Ionicons';
import { useNavigation } from '@react-navigation/native';
import api from '../../services/api';
import { MedicationReminder } from '../../types';
import EmptyState from '../../components/EmptyState';
import Button from '../../components/Button';

export default function MedicationRemindersScreen() {
  const navigation = useNavigation();

  const [reminders, setReminders] = useState<MedicationReminder[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadReminders();
  }, []);

  const loadReminders = async () => {
    try {
      const response = await api.getMedicationReminders();
      setReminders(response.data || []);
    } catch (error) {
      console.error('Error loading reminders:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadReminders();
  };

  const handleToggleReminder = async (id: number, currentActive: boolean) => {
    try {
      await api.updateMedicationReminder(id, { active: !currentActive });
      setReminders((prev) =>
        prev.map((reminder) =>
          reminder.id === id ? { ...reminder, active: !currentActive } : reminder
        )
      );
    } catch (error) {
      Alert.alert('Erreur', 'Impossible de modifier le rappel');
    }
  };

  const handleDeleteReminder = (id: number) => {
    Alert.alert(
      'Supprimer le rappel',
      'Êtes-vous sûr de vouloir supprimer ce rappel ?',
      [
        { text: 'Annuler', style: 'cancel' },
        {
          text: 'Supprimer',
          style: 'destructive',
          onPress: async () => {
            try {
              await api.deleteMedicationReminder(id);
              setReminders((prev) => prev.filter((r) => r.id !== id));
            } catch (error) {
              Alert.alert('Erreur', 'Impossible de supprimer le rappel');
            }
          },
        },
      ]
    );
  };

  const formatTimes = (times: string[]) => {
    return times.join(', ');
  };

  const renderReminderCard = ({ item }: { item: MedicationReminder }) => (
    <View style={styles.reminderCard}>
      <View style={styles.cardHeader}>
        <View style={styles.cardHeaderLeft}>
          <Icon
            name="alarm"
            size={24}
            color={item.active ? '#14B8A6' : '#9CA3AF'}
          />
          <View style={styles.cardHeaderInfo}>
            <Text style={styles.medicationName}>{item.medication_name}</Text>
            <Text style={styles.dosage}>{item.dosage}</Text>
          </View>
        </View>
        <Switch
          value={item.active}
          onValueChange={() => handleToggleReminder(item.id, item.active)}
          trackColor={{ false: '#D1D5DB', true: '#9FE2BF' }}
          thumbColor={item.active ? '#14B8A6' : '#F3F4F6'}
        />
      </View>

      <View style={styles.cardBody}>
        <View style={styles.infoRow}>
          <Icon name="time-outline" size={16} color="#6B7280" />
          <Text style={styles.infoText}>
            {item.times_per_day}x par jour: {formatTimes(item.reminder_times)}
          </Text>
        </View>
        <View style={styles.infoRow}>
          <Icon name="calendar-outline" size={16} color="#6B7280" />
          <Text style={styles.infoText}>
            {new Date(item.start_date).toLocaleDateString('fr-FR')}
            {item.end_date &&
              ` - ${new Date(item.end_date).toLocaleDateString('fr-FR')}`}
          </Text>
        </View>
        {item.notes && (
          <View style={styles.infoRow}>
            <Icon name="information-circle-outline" size={16} color="#6B7280" />
            <Text style={styles.infoText}>{item.notes}</Text>
          </View>
        )}
      </View>

      <View style={styles.cardFooter}>
        <TouchableOpacity
          style={styles.actionButton}
          onPress={() =>
            navigation.navigate('EditMedicationReminder' as never, { id: item.id } as never)
          }
        >
          <Icon name="create-outline" size={20} color="#14B8A6" />
          <Text style={styles.actionButtonText}>Modifier</Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.actionButton, styles.deleteButton]}
          onPress={() => handleDeleteReminder(item.id)}
        >
          <Icon name="trash-outline" size={20} color="#EF4444" />
          <Text style={[styles.actionButtonText, styles.deleteButtonText]}>
            Supprimer
          </Text>
        </TouchableOpacity>
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
        <Text style={styles.title}>Rappels médicaments</Text>
        <TouchableOpacity
          onPress={() => navigation.navigate('AddMedicationReminder' as never)}
        >
          <Icon name="add-circle" size={28} color="#14B8A6" />
        </TouchableOpacity>
      </View>

      <FlatList
        data={reminders}
        renderItem={renderReminderCard}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={styles.listContent}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        ListEmptyComponent={
          <EmptyState
            icon="alarm-outline"
            title="Aucun rappel"
            subtitle="Créez des rappels pour ne jamais oublier vos médicaments"
            action={
              <Button
                title="Ajouter un rappel"
                onPress={() => navigation.navigate('AddMedicationReminder' as never)}
                icon={<Icon name="add" size={20} color="#FFFFFF" />}
              />
            }
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
  reminderCard: {
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
  medicationName: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#111827',
    marginBottom: 2,
  },
  dosage: {
    fontSize: 14,
    color: '#6B7280',
  },
  cardBody: {
    marginBottom: 12,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginBottom: 6,
  },
  infoText: {
    fontSize: 14,
    color: '#374151',
    marginLeft: 8,
    flex: 1,
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
  deleteButton: {
    backgroundColor: '#FEE2E2',
  },
  deleteButtonText: {
    color: '#EF4444',
  },
});
