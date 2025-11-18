import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Icon from 'react-native-vector-icons/Ionicons';
import { useAuthStore } from '../../store/authStore';
import { useNavigation } from '@react-navigation/native';
import { Picker } from '@react-native-picker/picker';

export default function RegisterScreen() {
  const navigation = useNavigation();
  const register = useAuthStore((state) => state.register);

  const [formData, setFormData] = useState({
    role: 'patient',
    nom: '',
    prenom: '',
    email: '',
    telephone: '',
    password: '',
    password_confirmation: '',
    date_naissance: '',
    sexe: 'homme',
    adresse: '',
    ville: '',
  });

  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [loading, setLoading] = useState(false);

  const handleRegister = async () => {
    // Validation
    if (!formData.nom || !formData.prenom || !formData.email || !formData.password) {
      Alert.alert('Erreur', 'Veuillez remplir tous les champs obligatoires');
      return;
    }

    if (formData.password !== formData.password_confirmation) {
      Alert.alert('Erreur', 'Les mots de passe ne correspondent pas');
      return;
    }

    if (formData.password.length < 8) {
      Alert.alert('Erreur', 'Le mot de passe doit contenir au moins 8 caractères');
      return;
    }

    setLoading(true);
    try {
      await register(formData);
      Alert.alert('Succès', 'Votre compte a été créé avec succès', [
        {
          text: 'OK',
          onPress: () => navigation.navigate('Login' as never),
        },
      ]);
    } catch (error: any) {
      Alert.alert(
        'Erreur d\'inscription',
        error.response?.data?.message || 'Une erreur est survenue'
      );
    } finally {
      setLoading(false);
    }
  };

  const updateField = (field: string, value: string) => {
    setFormData({ ...formData, [field]: value });
  };

  return (
    <SafeAreaView style={styles.container}>
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        style={styles.keyboardView}
      >
        <ScrollView contentContainerStyle={styles.scrollContent}>
          {/* Header */}
          <View style={styles.header}>
            <Icon name="person-add" size={50} color="#14B8A6" />
            <Text style={styles.title}>Créer un compte</Text>
            <Text style={styles.subtitle}>Rejoignez Seha Digital</Text>
          </View>

          {/* Form */}
          <View style={styles.form}>
            {/* Role Selection */}
            <View style={styles.inputContainer}>
              <Icon name="people-outline" size={20} color="#6B7280" style={styles.inputIcon} />
              <Picker
                selectedValue={formData.role}
                onValueChange={(value) => updateField('role', value)}
                style={styles.picker}
              >
                <Picker.Item label="Patient" value="patient" />
                <Picker.Item label="Médecin" value="medecin" />
              </Picker>
            </View>

            {/* Nom */}
            <View style={styles.inputContainer}>
              <Icon name="person-outline" size={20} color="#6B7280" style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                placeholder="Nom *"
                placeholderTextColor="#9CA3AF"
                value={formData.nom}
                onChangeText={(value) => updateField('nom', value)}
              />
            </View>

            {/* Prénom */}
            <View style={styles.inputContainer}>
              <Icon name="person-outline" size={20} color="#6B7280" style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                placeholder="Prénom *"
                placeholderTextColor="#9CA3AF"
                value={formData.prenom}
                onChangeText={(value) => updateField('prenom', value)}
              />
            </View>

            {/* Email */}
            <View style={styles.inputContainer}>
              <Icon name="mail-outline" size={20} color="#6B7280" style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                placeholder="Email *"
                placeholderTextColor="#9CA3AF"
                value={formData.email}
                onChangeText={(value) => updateField('email', value)}
                keyboardType="email-address"
                autoCapitalize="none"
                autoCorrect={false}
              />
            </View>

            {/* Téléphone */}
            <View style={styles.inputContainer}>
              <Icon name="call-outline" size={20} color="#6B7280" style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                placeholder="Téléphone"
                placeholderTextColor="#9CA3AF"
                value={formData.telephone}
                onChangeText={(value) => updateField('telephone', value)}
                keyboardType="phone-pad"
              />
            </View>

            {/* Date de naissance */}
            <View style={styles.inputContainer}>
              <Icon name="calendar-outline" size={20} color="#6B7280" style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                placeholder="Date de naissance (YYYY-MM-DD)"
                placeholderTextColor="#9CA3AF"
                value={formData.date_naissance}
                onChangeText={(value) => updateField('date_naissance', value)}
              />
            </View>

            {/* Sexe */}
            <View style={styles.inputContainer}>
              <Icon name="male-female-outline" size={20} color="#6B7280" style={styles.inputIcon} />
              <Picker
                selectedValue={formData.sexe}
                onValueChange={(value) => updateField('sexe', value)}
                style={styles.picker}
              >
                <Picker.Item label="Homme" value="homme" />
                <Picker.Item label="Femme" value="femme" />
              </Picker>
            </View>

            {/* Ville */}
            <View style={styles.inputContainer}>
              <Icon name="location-outline" size={20} color="#6B7280" style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                placeholder="Ville"
                placeholderTextColor="#9CA3AF"
                value={formData.ville}
                onChangeText={(value) => updateField('ville', value)}
              />
            </View>

            {/* Adresse */}
            <View style={styles.inputContainer}>
              <Icon name="home-outline" size={20} color="#6B7280" style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                placeholder="Adresse"
                placeholderTextColor="#9CA3AF"
                value={formData.adresse}
                onChangeText={(value) => updateField('adresse', value)}
                multiline
              />
            </View>

            {/* Password */}
            <View style={styles.inputContainer}>
              <Icon name="lock-closed-outline" size={20} color="#6B7280" style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                placeholder="Mot de passe *"
                placeholderTextColor="#9CA3AF"
                value={formData.password}
                onChangeText={(value) => updateField('password', value)}
                secureTextEntry={!showPassword}
                autoCapitalize="none"
              />
              <TouchableOpacity
                onPress={() => setShowPassword(!showPassword)}
                style={styles.eyeIcon}
              >
                <Icon
                  name={showPassword ? 'eye-outline' : 'eye-off-outline'}
                  size={20}
                  color="#6B7280"
                />
              </TouchableOpacity>
            </View>

            {/* Confirm Password */}
            <View style={styles.inputContainer}>
              <Icon name="lock-closed-outline" size={20} color="#6B7280" style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                placeholder="Confirmer le mot de passe *"
                placeholderTextColor="#9CA3AF"
                value={formData.password_confirmation}
                onChangeText={(value) => updateField('password_confirmation', value)}
                secureTextEntry={!showConfirmPassword}
                autoCapitalize="none"
              />
              <TouchableOpacity
                onPress={() => setShowConfirmPassword(!showConfirmPassword)}
                style={styles.eyeIcon}
              >
                <Icon
                  name={showConfirmPassword ? 'eye-outline' : 'eye-off-outline'}
                  size={20}
                  color="#6B7280"
                />
              </TouchableOpacity>
            </View>

            {/* Register Button */}
            <TouchableOpacity
              style={[styles.registerButton, loading && styles.registerButtonDisabled]}
              onPress={handleRegister}
              disabled={loading}
            >
              {loading ? (
                <ActivityIndicator color="#FFFFFF" />
              ) : (
                <Text style={styles.registerButtonText}>S'inscrire</Text>
              )}
            </TouchableOpacity>

            {/* Login Link */}
            <View style={styles.loginContainer}>
              <Text style={styles.loginText}>Vous avez déjà un compte ? </Text>
              <TouchableOpacity onPress={() => navigation.navigate('Login' as never)}>
                <Text style={styles.loginLink}>Se connecter</Text>
              </TouchableOpacity>
            </View>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#FFFFFF',
  },
  keyboardView: {
    flex: 1,
  },
  scrollContent: {
    flexGrow: 1,
    padding: 24,
  },
  header: {
    alignItems: 'center',
    marginBottom: 32,
    marginTop: 20,
  },
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#111827',
    marginTop: 12,
  },
  subtitle: {
    fontSize: 14,
    color: '#6B7280',
    marginTop: 4,
  },
  form: {
    width: '100%',
  },
  inputContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#D1D5DB',
    borderRadius: 12,
    marginBottom: 12,
    paddingHorizontal: 16,
    backgroundColor: '#F9FAFB',
  },
  inputIcon: {
    marginRight: 12,
  },
  input: {
    flex: 1,
    height: 52,
    fontSize: 15,
    color: '#111827',
  },
  picker: {
    flex: 1,
    height: 52,
  },
  eyeIcon: {
    padding: 8,
  },
  registerButton: {
    backgroundColor: '#14B8A6',
    borderRadius: 12,
    height: 52,
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 8,
    shadowColor: '#14B8A6',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 4,
  },
  registerButtonDisabled: {
    backgroundColor: '#9CA3AF',
  },
  registerButtonText: {
    color: '#FFFFFF',
    fontSize: 16,
    fontWeight: 'bold',
  },
  loginContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 20,
  },
  loginText: {
    color: '#6B7280',
    fontSize: 14,
  },
  loginLink: {
    color: '#14B8A6',
    fontSize: 14,
    fontWeight: 'bold',
  },
});
