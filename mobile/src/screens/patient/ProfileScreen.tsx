import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Image,
  Alert,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Icon from 'react-native-vector-icons/Ionicons';
import { useNavigation } from '@react-navigation/native';
import { useAuthStore } from '../../store/authStore';

export default function ProfileScreen() {
  const navigation = useNavigation();
  const { user, logout } = useAuthStore();

  const handleLogout = () => {
    Alert.alert('Déconnexion', 'Êtes-vous sûr de vouloir vous déconnecter ?', [
      { text: 'Annuler', style: 'cancel' },
      {
        text: 'Déconnexion',
        style: 'destructive',
        onPress: async () => {
          await logout();
        },
      },
    ]);
  };

  const menuItems = [
    {
      icon: 'person-outline',
      label: 'Informations personnelles',
      onPress: () => navigation.navigate('EditProfile' as never),
    },
    {
      icon: 'document-text-outline',
      label: 'Mes ordonnances',
      onPress: () => navigation.navigate('Prescriptions' as never),
    },
    {
      icon: 'alarm-outline',
      label: 'Rappels médicaments',
      onPress: () => navigation.navigate('MedicationReminders' as never),
    },
    {
      icon: 'heart-outline',
      label: 'Médecins favoris',
      onPress: () => navigation.navigate('Favorites' as never),
    },
    {
      icon: 'card-outline',
      label: 'Moyens de paiement',
      onPress: () => navigation.navigate('PaymentMethods' as never),
    },
    {
      icon: 'notifications-outline',
      label: 'Notifications',
      onPress: () => navigation.navigate('NotificationSettings' as never),
    },
    {
      icon: 'shield-checkmark-outline',
      label: 'Confidentialité',
      onPress: () => navigation.navigate('Privacy' as never),
    },
    {
      icon: 'help-circle-outline',
      label: 'Aide et support',
      onPress: () => navigation.navigate('Support' as never),
    },
  ];

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        {/* Header */}
        <View style={styles.header}>
          <Text style={styles.title}>Profil</Text>
        </View>

        {/* Profile Card */}
        <View style={styles.profileCard}>
          <View style={styles.profileImageContainer}>
            {user?.photo_profil ? (
              <Image source={{ uri: user.photo_profil }} style={styles.profileImage} />
            ) : (
              <View style={styles.profileImagePlaceholder}>
                <Icon name="person" size={48} color="#9CA3AF" />
              </View>
            )}
            <TouchableOpacity style={styles.editPhotoButton}>
              <Icon name="camera" size={20} color="#FFFFFF" />
            </TouchableOpacity>
          </View>

          <Text style={styles.profileName}>
            {user?.prenom} {user?.nom}
          </Text>
          <Text style={styles.profileEmail}>{user?.email}</Text>
          {user?.telephone && (
            <Text style={styles.profilePhone}>{user.telephone}</Text>
          )}
        </View>

        {/* Menu Items */}
        <View style={styles.menuSection}>
          {menuItems.map((item, index) => (
            <TouchableOpacity
              key={index}
              style={styles.menuItem}
              onPress={item.onPress}
            >
              <View style={styles.menuItemLeft}>
                <View style={styles.menuItemIcon}>
                  <Icon name={item.icon} size={24} color="#14B8A6" />
                </View>
                <Text style={styles.menuItemLabel}>{item.label}</Text>
              </View>
              <Icon name="chevron-forward" size={20} color="#9CA3AF" />
            </TouchableOpacity>
          ))}
        </View>

        {/* App Info */}
        <View style={styles.appInfo}>
          <Text style={styles.appInfoText}>Version 4.0.0</Text>
          <Text style={styles.appInfoText}>Seha Digital © 2025</Text>
        </View>

        {/* Logout Button */}
        <TouchableOpacity style={styles.logoutButton} onPress={handleLogout}>
          <Icon name="log-out-outline" size={24} color="#EF4444" />
          <Text style={styles.logoutButtonText}>Se déconnecter</Text>
        </TouchableOpacity>
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
    paddingBottom: 100,
  },
  header: {
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
  profileCard: {
    backgroundColor: '#FFFFFF',
    margin: 16,
    borderRadius: 16,
    padding: 24,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  profileImageContainer: {
    position: 'relative',
    marginBottom: 16,
  },
  profileImage: {
    width: 100,
    height: 100,
    borderRadius: 50,
  },
  profileImagePlaceholder: {
    width: 100,
    height: 100,
    borderRadius: 50,
    backgroundColor: '#F3F4F6',
    justifyContent: 'center',
    alignItems: 'center',
  },
  editPhotoButton: {
    position: 'absolute',
    bottom: 0,
    right: 0,
    backgroundColor: '#14B8A6',
    width: 36,
    height: 36,
    borderRadius: 18,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 3,
    borderColor: '#FFFFFF',
  },
  profileName: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#111827',
    marginBottom: 4,
  },
  profileEmail: {
    fontSize: 14,
    color: '#6B7280',
    marginBottom: 2,
  },
  profilePhone: {
    fontSize: 14,
    color: '#6B7280',
  },
  menuSection: {
    backgroundColor: '#FFFFFF',
    marginHorizontal: 16,
    borderRadius: 16,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  menuItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 16,
    paddingHorizontal: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#F3F4F6',
  },
  menuItemLeft: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  menuItemIcon: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: '#ECFDF5',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  menuItemLabel: {
    fontSize: 16,
    color: '#111827',
  },
  appInfo: {
    alignItems: 'center',
    marginTop: 24,
    marginBottom: 16,
  },
  appInfoText: {
    fontSize: 12,
    color: '#9CA3AF',
    marginBottom: 4,
  },
  logoutButton: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    marginHorizontal: 16,
    borderRadius: 12,
    paddingVertical: 16,
    borderWidth: 1,
    borderColor: '#FEE2E2',
  },
  logoutButtonText: {
    fontSize: 16,
    color: '#EF4444',
    fontWeight: 'bold',
    marginLeft: 8,
  },
});
