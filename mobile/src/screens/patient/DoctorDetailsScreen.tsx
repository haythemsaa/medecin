import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  Image,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Icon from 'react-native-vector-icons/Ionicons';
import { useNavigation, useRoute } from '@react-navigation/native';
import api from '../../services/api';
import { Medecin, Review } from '../../types';
import Button from '../../components/Button';

export default function DoctorDetailsScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const { id } = route.params as { id: number };

  const [doctor, setDoctor] = useState<Medecin | null>(null);
  const [reviews, setReviews] = useState<Review[]>([]);
  const [isFavorite, setIsFavorite] = useState(false);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadDoctorDetails();
  }, [id]);

  const loadDoctorDetails = async () => {
    try {
      const [doctorRes, reviewsRes, favoritesRes] = await Promise.all([
        api.getDoctorDetails(id),
        api.getDoctorReviews(id),
        api.getFavorites(),
      ]);

      setDoctor(doctorRes);
      setReviews(reviewsRes.data || []);
      setIsFavorite(favoritesRes.some((f: any) => f.medecin_id === id));
    } catch (error) {
      console.error('Error loading doctor details:', error);
      Alert.alert('Erreur', 'Impossible de charger les détails du médecin');
    } finally {
      setLoading(false);
    }
  };

  const handleToggleFavorite = async () => {
    try {
      if (isFavorite) {
        await api.removeFavorite(id);
        setIsFavorite(false);
      } else {
        await api.addFavorite(id);
        setIsFavorite(true);
      }
    } catch (error) {
      Alert.alert('Erreur', 'Impossible de modifier les favoris');
    }
  };

  const handleBookAppointment = () => {
    navigation.navigate('BookAppointment' as never, { doctorId: id } as never);
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#14B8A6" />
      </View>
    );
  }

  if (!doctor) {
    return (
      <View style={styles.errorContainer}>
        <Text style={styles.errorText}>Médecin introuvable</Text>
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
        <TouchableOpacity onPress={handleToggleFavorite}>
          <Icon
            name={isFavorite ? 'heart' : 'heart-outline'}
            size={24}
            color={isFavorite ? '#EF4444' : '#111827'}
          />
        </TouchableOpacity>
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent}>
        {/* Doctor Info Card */}
        <View style={styles.doctorCard}>
          {doctor.user.photo_profil ? (
            <Image
              source={{ uri: doctor.user.photo_profil }}
              style={styles.doctorImage}
            />
          ) : (
            <View style={styles.doctorImagePlaceholder}>
              <Icon name="person" size={64} color="#9CA3AF" />
            </View>
          )}

          <Text style={styles.doctorName}>
            Dr. {doctor.user.prenom} {doctor.user.nom}
          </Text>
          <Text style={styles.doctorSpecialty}>{doctor.specialite}</Text>

          {doctor.rating !== undefined && (
            <View style={styles.ratingContainer}>
              <Icon name="star" size={20} color="#F59E0B" />
              <Text style={styles.rating}>{doctor.rating.toFixed(1)}</Text>
              <Text style={styles.reviewCount}>
                ({doctor.total_reviews || 0} avis)
              </Text>
            </View>
          )}

          <View style={styles.statsContainer}>
            <View style={styles.stat}>
              <Text style={styles.statValue}>{doctor.experience_annees || 0}+</Text>
              <Text style={styles.statLabel}>Années</Text>
            </View>
            <View style={styles.statDivider} />
            <View style={styles.stat}>
              <Text style={styles.statValue}>{doctor.total_reviews || 0}</Text>
              <Text style={styles.statLabel}>Avis</Text>
            </View>
            <View style={styles.statDivider} />
            <View style={styles.stat}>
              <Text style={styles.statValue}>{doctor.tarif} TND</Text>
              <Text style={styles.statLabel}>Tarif</Text>
            </View>
          </View>
        </View>

        {/* About */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>À propos</Text>
          <View style={styles.infoRow}>
            <Icon name="location" size={20} color="#14B8A6" />
            <Text style={styles.infoText}>
              {doctor.adresse}, {doctor.ville}
            </Text>
          </View>
          {doctor.langues_parlees && doctor.langues_parlees.length > 0 && (
            <View style={styles.infoRow}>
              <Icon name="language" size={20} color="#14B8A6" />
              <Text style={styles.infoText}>
                {doctor.langues_parlees.join(', ')}
              </Text>
            </View>
          )}
          {doctor.formation && (
            <View style={styles.infoRow}>
              <Icon name="school" size={20} color="#14B8A6" />
              <Text style={styles.infoText}>{doctor.formation}</Text>
            </View>
          )}
        </View>

        {/* Reviews */}
        {reviews.length > 0 && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Avis des patients</Text>
            {reviews.slice(0, 3).map((review) => (
              <View key={review.id} style={styles.reviewCard}>
                <View style={styles.reviewHeader}>
                  <Text style={styles.reviewPatient}>
                    {review.patient?.user.prenom}
                  </Text>
                  <View style={styles.reviewRating}>
                    <Icon name="star" size={16} color="#F59E0B" />
                    <Text style={styles.reviewRatingText}>{review.rating}</Text>
                  </View>
                </View>
                {review.comment && (
                  <Text style={styles.reviewComment}>{review.comment}</Text>
                )}
                <Text style={styles.reviewDate}>
                  {new Date(review.created_at).toLocaleDateString('fr-FR')}
                </Text>
              </View>
            ))}
          </View>
        )}
      </ScrollView>

      {/* Book Appointment Button */}
      <View style={styles.footer}>
        <Button
          title="Prendre rendez-vous"
          onPress={handleBookAppointment}
          icon={<Icon name="calendar" size={20} color="#FFFFFF" />}
        />
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F9FAFB',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
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
  scrollContent: {
    paddingBottom: 100,
  },
  doctorCard: {
    backgroundColor: '#FFFFFF',
    padding: 24,
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
  },
  doctorImage: {
    width: 120,
    height: 120,
    borderRadius: 60,
    marginBottom: 16,
  },
  doctorImagePlaceholder: {
    width: 120,
    height: 120,
    borderRadius: 60,
    backgroundColor: '#F3F4F6',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 16,
  },
  doctorName: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#111827',
    marginBottom: 4,
  },
  doctorSpecialty: {
    fontSize: 16,
    color: '#6B7280',
    marginBottom: 12,
  },
  ratingContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 20,
  },
  rating: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#111827',
    marginLeft: 6,
  },
  reviewCount: {
    fontSize: 14,
    color: '#6B7280',
    marginLeft: 6,
  },
  statsContainer: {
    flexDirection: 'row',
    width: '100%',
    justifyContent: 'space-around',
    paddingTop: 20,
  },
  stat: {
    alignItems: 'center',
  },
  statValue: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#14B8A6',
    marginBottom: 4,
  },
  statLabel: {
    fontSize: 12,
    color: '#6B7280',
  },
  statDivider: {
    width: 1,
    backgroundColor: '#E5E7EB',
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
  infoRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginBottom: 12,
  },
  infoText: {
    flex: 1,
    fontSize: 14,
    color: '#374151',
    marginLeft: 12,
    lineHeight: 20,
  },
  reviewCard: {
    backgroundColor: '#F9FAFB',
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
  },
  reviewHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  reviewPatient: {
    fontSize: 16,
    fontWeight: '600',
    color: '#111827',
  },
  reviewRating: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  reviewRatingText: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#111827',
    marginLeft: 4,
  },
  reviewComment: {
    fontSize: 14,
    color: '#374151',
    lineHeight: 20,
    marginBottom: 8,
  },
  reviewDate: {
    fontSize: 12,
    color: '#9CA3AF',
  },
  footer: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    padding: 20,
    backgroundColor: '#FFFFFF',
    borderTopWidth: 1,
    borderTopColor: '#E5E7EB',
  },
});
