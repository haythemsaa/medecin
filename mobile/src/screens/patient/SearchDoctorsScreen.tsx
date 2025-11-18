import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  TextInput,
  ActivityIndicator,
  Image,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Icon from 'react-native-vector-icons/Ionicons';
import { useNavigation } from '@react-navigation/native';
import api from '../../services/api';

interface Doctor {
  id: number;
  specialite: string;
  tarif: number;
  adresse: string;
  ville: string;
  distance?: number;
  user: {
    nom: string;
    prenom: string;
    photo_profil?: string;
  };
}

const SPECIALTIES = [
  'Tous',
  'Médecin généraliste',
  'Cardiologue',
  'Dermatologue',
  'Pédiatre',
  'Gynécologue',
  'Dentiste',
  'Ophtalmologue',
  'ORL',
  'Psychiatre',
  'Nutritionniste',
];

export default function SearchDoctorsScreen() {
  const navigation = useNavigation();

  const [doctors, setDoctors] = useState<Doctor[]>([]);
  const [filteredDoctors, setFilteredDoctors] = useState<Doctor[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedSpecialty, setSelectedSpecialty] = useState('Tous');
  const [viewMode, setViewMode] = useState<'list' | 'map'>('list');

  useEffect(() => {
    loadDoctors();
  }, []);

  useEffect(() => {
    filterDoctors();
  }, [doctors, searchQuery, selectedSpecialty]);

  const loadDoctors = async () => {
    try {
      const response = await api.searchDoctors({});
      setDoctors(response.data);
    } catch (error) {
      console.error('Error loading doctors:', error);
    } finally {
      setLoading(false);
    }
  };

  const filterDoctors = () => {
    let filtered = [...doctors];

    // Filter by specialty
    if (selectedSpecialty !== 'Tous') {
      filtered = filtered.filter((doc) => doc.specialite === selectedSpecialty);
    }

    // Filter by search query
    if (searchQuery.trim()) {
      const query = searchQuery.toLowerCase();
      filtered = filtered.filter(
        (doc) =>
          doc.user.nom.toLowerCase().includes(query) ||
          doc.user.prenom.toLowerCase().includes(query) ||
          doc.specialite.toLowerCase().includes(query) ||
          doc.ville.toLowerCase().includes(query)
      );
    }

    setFilteredDoctors(filtered);
  };

  const renderDoctorCard = ({ item }: { item: Doctor }) => (
    <TouchableOpacity
      style={styles.doctorCard}
      onPress={() => navigation.navigate('DoctorDetails' as never, { id: item.id } as never)}
    >
      <View style={styles.doctorImageContainer}>
        {item.user.photo_profil ? (
          <Image source={{ uri: item.user.photo_profil }} style={styles.doctorImage} />
        ) : (
          <View style={styles.doctorImagePlaceholder}>
            <Icon name="person" size={32} color="#9CA3AF" />
          </View>
        )}
      </View>

      <View style={styles.doctorInfo}>
        <Text style={styles.doctorName}>
          Dr. {item.user.prenom} {item.user.nom}
        </Text>
        <Text style={styles.doctorSpecialty}>{item.specialite}</Text>
        <View style={styles.doctorMeta}>
          <Icon name="location-outline" size={14} color="#6B7280" />
          <Text style={styles.doctorMetaText}>{item.ville}</Text>
        </View>
        {item.distance && (
          <View style={styles.doctorMeta}>
            <Icon name="navigate-outline" size={14} color="#14B8A6" />
            <Text style={styles.doctorDistanceText}>{item.distance.toFixed(1)} km</Text>
          </View>
        )}
      </View>

      <View style={styles.doctorActions}>
        <Text style={styles.doctorPrice}>{item.tarif} TND</Text>
        <Icon name="chevron-forward" size={20} color="#9CA3AF" />
      </View>
    </TouchableOpacity>
  );

  return (
    <SafeAreaView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <Text style={styles.title}>Trouver un médecin</Text>
        <View style={styles.headerActions}>
          <TouchableOpacity
            style={styles.viewModeButton}
            onPress={() => setViewMode(viewMode === 'list' ? 'map' : 'list')}
          >
            <Icon name={viewMode === 'list' ? 'map-outline' : 'list-outline'} size={24} color="#111827" />
          </TouchableOpacity>
        </View>
      </View>

      {/* Search Bar */}
      <View style={styles.searchContainer}>
        <Icon name="search" size={20} color="#6B7280" style={styles.searchIcon} />
        <TextInput
          style={styles.searchInput}
          placeholder="Rechercher par nom, spécialité, ville..."
          placeholderTextColor="#9CA3AF"
          value={searchQuery}
          onChangeText={setSearchQuery}
        />
        {searchQuery.length > 0 && (
          <TouchableOpacity onPress={() => setSearchQuery('')}>
            <Icon name="close-circle" size={20} color="#9CA3AF" />
          </TouchableOpacity>
        )}
      </View>

      {/* Specialty Filter */}
      <View style={styles.specialtyContainer}>
        <FlatList
          horizontal
          showsHorizontalScrollIndicator={false}
          data={SPECIALTIES}
          keyExtractor={(item) => item}
          renderItem={({ item }) => (
            <TouchableOpacity
              style={[
                styles.specialtyChip,
                selectedSpecialty === item && styles.specialtyChipActive,
              ]}
              onPress={() => setSelectedSpecialty(item)}
            >
              <Text
                style={[
                  styles.specialtyChipText,
                  selectedSpecialty === item && styles.specialtyChipTextActive,
                ]}
              >
                {item}
              </Text>
            </TouchableOpacity>
          )}
        />
      </View>

      {/* Results */}
      {loading ? (
        <ActivityIndicator size="large" color="#14B8A6" style={styles.loader} />
      ) : viewMode === 'map' ? (
        <View style={styles.mapPlaceholder}>
          <Icon name="map" size={64} color="#9CA3AF" />
          <Text style={styles.mapPlaceholderText}>Vue carte sera disponible bientôt</Text>
          <Text style={styles.mapPlaceholderSubtext}>Utilisez react-native-maps</Text>
        </View>
      ) : (
        <FlatList
          data={filteredDoctors}
          renderItem={renderDoctorCard}
          keyExtractor={(item) => item.id.toString()}
          contentContainerStyle={styles.listContent}
          ListEmptyComponent={
            <View style={styles.emptyState}>
              <Icon name="search-outline" size={64} color="#9CA3AF" />
              <Text style={styles.emptyStateText}>Aucun médecin trouvé</Text>
              <Text style={styles.emptyStateSubtext}>Essayez de modifier vos critères de recherche</Text>
            </View>
          }
        />
      )}
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
    fontSize: 24,
    fontWeight: 'bold',
    color: '#111827',
  },
  headerActions: {
    flexDirection: 'row',
  },
  viewModeButton: {
    padding: 8,
  },
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    margin: 16,
    paddingHorizontal: 16,
    paddingVertical: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  searchIcon: {
    marginRight: 8,
  },
  searchInput: {
    flex: 1,
    fontSize: 16,
    color: '#111827',
  },
  specialtyContainer: {
    paddingHorizontal: 16,
    marginBottom: 16,
  },
  specialtyChip: {
    backgroundColor: '#FFFFFF',
    borderRadius: 20,
    paddingHorizontal: 16,
    paddingVertical: 8,
    marginRight: 8,
    borderWidth: 1,
    borderColor: '#E5E7EB',
  },
  specialtyChipActive: {
    backgroundColor: '#14B8A6',
    borderColor: '#14B8A6',
  },
  specialtyChipText: {
    fontSize: 14,
    color: '#6B7280',
  },
  specialtyChipTextActive: {
    color: '#FFFFFF',
    fontWeight: 'bold',
  },
  loader: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  listContent: {
    paddingHorizontal: 16,
    paddingBottom: 100,
  },
  doctorCard: {
    flexDirection: 'row',
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
  doctorImageContainer: {
    marginRight: 12,
  },
  doctorImage: {
    width: 64,
    height: 64,
    borderRadius: 32,
  },
  doctorImagePlaceholder: {
    width: 64,
    height: 64,
    borderRadius: 32,
    backgroundColor: '#F3F4F6',
    justifyContent: 'center',
    alignItems: 'center',
  },
  doctorInfo: {
    flex: 1,
  },
  doctorName: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#111827',
    marginBottom: 4,
  },
  doctorSpecialty: {
    fontSize: 14,
    color: '#6B7280',
    marginBottom: 8,
  },
  doctorMeta: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 2,
  },
  doctorMetaText: {
    fontSize: 12,
    color: '#6B7280',
    marginLeft: 4,
  },
  doctorDistanceText: {
    fontSize: 12,
    color: '#14B8A6',
    marginLeft: 4,
    fontWeight: '600',
  },
  doctorActions: {
    alignItems: 'flex-end',
    justifyContent: 'space-between',
  },
  doctorPrice: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#14B8A6',
    marginBottom: 8,
  },
  emptyState: {
    alignItems: 'center',
    paddingVertical: 64,
  },
  emptyStateText: {
    fontSize: 18,
    fontWeight: '600',
    color: '#6B7280',
    marginTop: 16,
  },
  emptyStateSubtext: {
    fontSize: 14,
    color: '#9CA3AF',
    marginTop: 8,
  },
  mapPlaceholder: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 32,
  },
  mapPlaceholderText: {
    fontSize: 18,
    fontWeight: '600',
    color: '#6B7280',
    marginTop: 16,
  },
  mapPlaceholderSubtext: {
    fontSize: 14,
    color: '#9CA3AF',
    marginTop: 8,
  },
});
