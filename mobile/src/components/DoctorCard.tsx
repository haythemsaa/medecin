import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  Image,
  TouchableOpacity,
  ViewStyle,
} from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';
import { Medecin } from '../types';

interface DoctorCardProps {
  doctor: Medecin;
  onPress: () => void;
  onFavoritePress?: () => void;
  isFavorite?: boolean;
  style?: ViewStyle;
}

export default function DoctorCard({
  doctor,
  onPress,
  onFavoritePress,
  isFavorite = false,
  style,
}: DoctorCardProps) {
  return (
    <TouchableOpacity style={[styles.card, style]} onPress={onPress}>
      <View style={styles.imageContainer}>
        {doctor.user.photo_profil ? (
          <Image
            source={{ uri: doctor.user.photo_profil }}
            style={styles.image}
          />
        ) : (
          <View style={styles.imagePlaceholder}>
            <Icon name="person" size={32} color="#9CA3AF" />
          </View>
        )}
        {onFavoritePress && (
          <TouchableOpacity
            style={styles.favoriteButton}
            onPress={onFavoritePress}
          >
            <Icon
              name={isFavorite ? 'heart' : 'heart-outline'}
              size={20}
              color={isFavorite ? '#EF4444' : '#6B7280'}
            />
          </TouchableOpacity>
        )}
      </View>

      <View style={styles.info}>
        <Text style={styles.name}>
          Dr. {doctor.user.prenom} {doctor.user.nom}
        </Text>
        <Text style={styles.specialty}>{doctor.specialite}</Text>

        <View style={styles.meta}>
          <View style={styles.metaItem}>
            <Icon name="location-outline" size={14} color="#6B7280" />
            <Text style={styles.metaText}>{doctor.ville}</Text>
          </View>
          {doctor.distance && (
            <View style={styles.metaItem}>
              <Icon name="navigate-outline" size={14} color="#14B8A6" />
              <Text style={styles.distanceText}>
                {doctor.distance.toFixed(1)} km
              </Text>
            </View>
          )}
        </View>

        {doctor.rating !== undefined && (
          <View style={styles.rating}>
            <Icon name="star" size={16} color="#F59E0B" />
            <Text style={styles.ratingText}>
              {doctor.rating.toFixed(1)}
            </Text>
            {doctor.total_reviews !== undefined && (
              <Text style={styles.reviewCount}>
                ({doctor.total_reviews} avis)
              </Text>
            )}
          </View>
        )}
      </View>

      <View style={styles.actions}>
        <Text style={styles.price}>{doctor.tarif} TND</Text>
        <Icon name="chevron-forward" size={20} color="#9CA3AF" />
      </View>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  card: {
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
  imageContainer: {
    position: 'relative',
    marginRight: 12,
  },
  image: {
    width: 64,
    height: 64,
    borderRadius: 32,
  },
  imagePlaceholder: {
    width: 64,
    height: 64,
    borderRadius: 32,
    backgroundColor: '#F3F4F6',
    justifyContent: 'center',
    alignItems: 'center',
  },
  favoriteButton: {
    position: 'absolute',
    top: -4,
    right: -4,
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    width: 24,
    height: 24,
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.2,
    shadowRadius: 2,
    elevation: 2,
  },
  info: {
    flex: 1,
  },
  name: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#111827',
    marginBottom: 4,
  },
  specialty: {
    fontSize: 14,
    color: '#6B7280',
    marginBottom: 8,
  },
  meta: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    marginBottom: 4,
  },
  metaItem: {
    flexDirection: 'row',
    alignItems: 'center',
    marginRight: 12,
    marginBottom: 2,
  },
  metaText: {
    fontSize: 12,
    color: '#6B7280',
    marginLeft: 4,
  },
  distanceText: {
    fontSize: 12,
    color: '#14B8A6',
    marginLeft: 4,
    fontWeight: '600',
  },
  rating: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  ratingText: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#111827',
    marginLeft: 4,
  },
  reviewCount: {
    fontSize: 12,
    color: '#6B7280',
    marginLeft: 4,
  },
  actions: {
    alignItems: 'flex-end',
    justifyContent: 'space-between',
  },
  price: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#14B8A6',
    marginBottom: 8,
  },
});
