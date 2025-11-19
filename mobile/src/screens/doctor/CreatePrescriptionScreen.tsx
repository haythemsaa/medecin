import React, { useState } from 'react';
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
import Input from '../../components/Input';
import Button from '../../components/Button';
import { Medicament } from '../../types';

export default function CreatePrescriptionScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const { appointmentId } = route.params as { appointmentId: number };

  const [medicaments, setMedicaments] = useState<Medicament[]>([
    { nom: '', dosage: '', frequence: '', duree: '', instructions: '' },
  ]);
  const [instructions, setInstructions] = useState('');
  const [dureeTraitement, setDureeTraitement] = useState('');
  const [renewable, setRenewable] = useState(false);
  const [maxRenewals, setMaxRenewals] = useState('3');
  const [loading, setLoading] = useState(false);

  const addMedicament = () => {
    setMedicaments([
      ...medicaments,
      { nom: '', dosage: '', frequence: '', duree: '', instructions: '' },
    ]);
  };

  const removeMedicament = (index: number) => {
    if (medicaments.length === 1) {
      Alert.alert('Erreur', 'Vous devez avoir au moins un médicament');
      return;
    }
    const newMedicaments = medicaments.filter((_, i) => i !== index);
    setMedicaments(newMedicaments);
  };

  const updateMedicament = (index: number, field: keyof Medicament, value: string) => {
    const newMedicaments = [...medicaments];
    newMedicaments[index] = { ...newMedicaments[index], [field]: value };
    setMedicaments(newMedicaments);
  };

  const validateForm = () => {
    for (const med of medicaments) {
      if (!med.nom || !med.dosage || !med.frequence || !med.duree) {
        Alert.alert('Erreur', 'Veuillez remplir tous les champs obligatoires pour chaque médicament');
        return false;
      }
    }
    return true;
  };

  const handleCreatePrescription = async () => {
    if (!validateForm()) return;

    setLoading(true);
    try {
      await api.createPrescription({
        appointment_id: appointmentId,
        medicaments,
        instructions,
        duree_traitement: dureeTraitement,
        renewable,
        max_renewals: renewable ? parseInt(maxRenewals) : undefined,
      });

      Alert.alert('Succès', 'Ordonnance créée avec succès', [
        { text: 'OK', onPress: () => navigation.goBack() },
      ]);
    } catch (error: any) {
      Alert.alert('Erreur', error.response?.data?.message || 'Impossible de créer l\'ordonnance');
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Icon name="arrow-back" size={24} color="#111827" />
        </TouchableOpacity>
        <Text style={styles.title}>Créer une ordonnance</Text>
        <View style={{ width: 24 }} />
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent}>
        {/* Medications */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <Text style={styles.sectionTitle}>Médicaments</Text>
            <TouchableOpacity style={styles.addButton} onPress={addMedicament}>
              <Icon name="add-circle" size={24} color="#14B8A6" />
              <Text style={styles.addButtonText}>Ajouter</Text>
            </TouchableOpacity>
          </View>

          {medicaments.map((med, index) => (
            <View key={index} style={styles.medicamentCard}>
              <View style={styles.medicamentHeader}>
                <Text style={styles.medicamentNumber}>Médicament {index + 1}</Text>
                {medicaments.length > 1 && (
                  <TouchableOpacity onPress={() => removeMedicament(index)}>
                    <Icon name="trash-outline" size={20} color="#EF4444" />
                  </TouchableOpacity>
                )}
              </View>

              <Input
                label="Nom du médicament *"
                placeholder="Ex: Paracétamol"
                value={med.nom}
                onChangeText={(value) => updateMedicament(index, 'nom', value)}
                icon="medical-outline"
              />

              <Input
                label="Dosage *"
                placeholder="Ex: 500mg"
                value={med.dosage}
                onChangeText={(value) => updateMedicament(index, 'dosage', value)}
                icon="flask-outline"
              />

              <Input
                label="Fréquence *"
                placeholder="Ex: 3 fois par jour"
                value={med.frequence}
                onChangeText={(value) => updateMedicament(index, 'frequence', value)}
                icon="time-outline"
              />

              <Input
                label="Durée *"
                placeholder="Ex: 7 jours"
                value={med.duree}
                onChangeText={(value) => updateMedicament(index, 'duree', value)}
                icon="calendar-outline"
              />

              <Input
                label="Instructions (optionnel)"
                placeholder="Ex: À prendre après les repas"
                value={med.instructions}
                onChangeText={(value) => updateMedicament(index, 'instructions', value)}
                multiline
                numberOfLines={2}
              />
            </View>
          ))}
        </View>

        {/* Global Instructions */}
        <View style={styles.section}>
          <Input
            label="Instructions générales"
            placeholder="Recommandations générales pour le patient..."
            value={instructions}
            onChangeText={setInstructions}
            multiline
            numberOfLines={4}
            textAlignVertical="top"
          />

          <Input
            label="Durée totale du traitement"
            placeholder="Ex: 2 semaines"
            value={dureeTraitement}
            onChangeText={setDureeTraitement}
            icon="hourglass-outline"
          />
        </View>

        {/* Renewable Option */}
        <View style={styles.section}>
          <TouchableOpacity
            style={styles.checkboxContainer}
            onPress={() => setRenewable(!renewable)}
          >
            <Icon
              name={renewable ? 'checkbox' : 'square-outline'}
              size={24}
              color={renewable ? '#14B8A6' : '#9CA3AF'}
            />
            <View style={styles.checkboxText}>
              <Text style={styles.checkboxLabel}>Ordonnance renouvelable</Text>
              <Text style={styles.checkboxSubtext}>
                Le patient pourra demander un renouvellement sans consultation
              </Text>
            </View>
          </TouchableOpacity>

          {renewable && (
            <Input
              label="Nombre maximum de renouvellements"
              placeholder="Ex: 3"
              value={maxRenewals}
              onChangeText={setMaxRenewals}
              keyboardType="number-pad"
              icon="reload-outline"
              containerStyle={{ marginTop: 12 }}
            />
          )}
        </View>

        {/* Create Button */}
        <View style={styles.section}>
          <Button
            title="Créer l'ordonnance"
            onPress={handleCreatePrescription}
            loading={loading}
            icon={<Icon name="document-text" size={20} color="#FFFFFF" />}
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
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 16,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#111827',
  },
  addButton: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  addButtonText: {
    fontSize: 14,
    color: '#14B8A6',
    fontWeight: '600',
    marginLeft: 4,
  },
  medicamentCard: {
    backgroundColor: '#F9FAFB',
    borderRadius: 12,
    padding: 16,
    marginBottom: 16,
  },
  medicamentHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  medicamentNumber: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#111827',
  },
  checkboxContainer: {
    flexDirection: 'row',
    alignItems: 'flex-start',
  },
  checkboxText: {
    flex: 1,
    marginLeft: 12,
  },
  checkboxLabel: {
    fontSize: 16,
    fontWeight: '600',
    color: '#111827',
    marginBottom: 4,
  },
  checkboxSubtext: {
    fontSize: 13,
    color: '#6B7280',
    lineHeight: 18,
  },
});
