import { Platform, PermissionsAndroid, Alert } from 'react-native';
import { check, request, PERMISSIONS, RESULTS } from 'react-native-permissions';

/**
 * Permission utilities
 */

export const permissions = {
  /**
   * Request camera permission
   */
  camera: async (): Promise<boolean> => {
    try {
      if (Platform.OS === 'android') {
        const granted = await PermissionsAndroid.request(
          PermissionsAndroid.PERMISSIONS.CAMERA,
          {
            title: 'Permission d\'accès à la caméra',
            message: 'Seha Digital a besoin d\'accéder à votre caméra pour les consultations vidéo',
            buttonNeutral: 'Plus tard',
            buttonNegative: 'Refuser',
            buttonPositive: 'Autoriser',
          }
        );
        return granted === PermissionsAndroid.RESULTS.GRANTED;
      } else {
        const result = await request(PERMISSIONS.IOS.CAMERA);
        return result === RESULTS.GRANTED;
      }
    } catch (error) {
      console.error('Camera permission error:', error);
      return false;
    }
  },

  /**
   * Request microphone permission
   */
  microphone: async (): Promise<boolean> => {
    try {
      if (Platform.OS === 'android') {
        const granted = await PermissionsAndroid.request(
          PermissionsAndroid.PERMISSIONS.RECORD_AUDIO,
          {
            title: 'Permission d\'accès au microphone',
            message: 'Seha Digital a besoin d\'accéder à votre microphone pour les consultations vidéo',
            buttonNeutral: 'Plus tard',
            buttonNegative: 'Refuser',
            buttonPositive: 'Autoriser',
          }
        );
        return granted === PermissionsAndroid.RESULTS.GRANTED;
      } else {
        const result = await request(PERMISSIONS.IOS.MICROPHONE);
        return result === RESULTS.GRANTED;
      }
    } catch (error) {
      console.error('Microphone permission error:', error);
      return false;
    }
  },

  /**
   * Request location permission
   */
  location: async (): Promise<boolean> => {
    try {
      if (Platform.OS === 'android') {
        const granted = await PermissionsAndroid.request(
          PermissionsAndroid.PERMISSIONS.ACCESS_FINE_LOCATION,
          {
            title: 'Permission d\'accès à la localisation',
            message: 'Seha Digital a besoin de votre position pour trouver des médecins à proximité',
            buttonNeutral: 'Plus tard',
            buttonNegative: 'Refuser',
            buttonPositive: 'Autoriser',
          }
        );
        return granted === PermissionsAndroid.RESULTS.GRANTED;
      } else {
        const result = await request(PERMISSIONS.IOS.LOCATION_WHEN_IN_USE);
        return result === RESULTS.GRANTED;
      }
    } catch (error) {
      console.error('Location permission error:', error);
      return false;
    }
  },

  /**
   * Request storage permission (for file upload)
   */
  storage: async (): Promise<boolean> => {
    try {
      if (Platform.OS === 'android') {
        if (Platform.Version >= 33) {
          // Android 13+
          const granted = await PermissionsAndroid.requestMultiple([
            PermissionsAndroid.PERMISSIONS.READ_MEDIA_IMAGES,
            PermissionsAndroid.PERMISSIONS.READ_MEDIA_VIDEO,
          ]);
          return (
            granted['android.permission.READ_MEDIA_IMAGES'] === PermissionsAndroid.RESULTS.GRANTED ||
            granted['android.permission.READ_MEDIA_VIDEO'] === PermissionsAndroid.RESULTS.GRANTED
          );
        } else {
          const granted = await PermissionsAndroid.request(
            PermissionsAndroid.PERMISSIONS.READ_EXTERNAL_STORAGE,
            {
              title: 'Permission d\'accès au stockage',
              message: 'Seha Digital a besoin d\'accéder à vos fichiers pour télécharger des documents',
              buttonNeutral: 'Plus tard',
              buttonNegative: 'Refuser',
              buttonPositive: 'Autoriser',
            }
          );
          return granted === PermissionsAndroid.RESULTS.GRANTED;
        }
      } else {
        const result = await request(PERMISSIONS.IOS.PHOTO_LIBRARY);
        return result === RESULTS.GRANTED;
      }
    } catch (error) {
      console.error('Storage permission error:', error);
      return false;
    }
  },

  /**
   * Request all video consultation permissions
   */
  videoCall: async (): Promise<boolean> => {
    const cameraGranted = await permissions.camera();
    if (!cameraGranted) {
      Alert.alert(
        'Permission requise',
        'L\'accès à la caméra est nécessaire pour les consultations vidéo'
      );
      return false;
    }

    const micGranted = await permissions.microphone();
    if (!micGranted) {
      Alert.alert(
        'Permission requise',
        'L\'accès au microphone est nécessaire pour les consultations vidéo'
      );
      return false;
    }

    return true;
  },

  /**
   * Check if permission is granted
   */
  check: async (permission: 'camera' | 'microphone' | 'location' | 'storage'): Promise<boolean> => {
    try {
      let perm;
      if (Platform.OS === 'android') {
        switch (permission) {
          case 'camera':
            perm = PermissionsAndroid.PERMISSIONS.CAMERA;
            break;
          case 'microphone':
            perm = PermissionsAndroid.PERMISSIONS.RECORD_AUDIO;
            break;
          case 'location':
            perm = PermissionsAndroid.PERMISSIONS.ACCESS_FINE_LOCATION;
            break;
          case 'storage':
            perm = PermissionsAndroid.PERMISSIONS.READ_EXTERNAL_STORAGE;
            break;
          default:
            return false;
        }
        const result = await PermissionsAndroid.check(perm);
        return result;
      } else {
        switch (permission) {
          case 'camera':
            perm = PERMISSIONS.IOS.CAMERA;
            break;
          case 'microphone':
            perm = PERMISSIONS.IOS.MICROPHONE;
            break;
          case 'location':
            perm = PERMISSIONS.IOS.LOCATION_WHEN_IN_USE;
            break;
          case 'storage':
            perm = PERMISSIONS.IOS.PHOTO_LIBRARY;
            break;
          default:
            return false;
        }
        const result = await check(perm);
        return result === RESULTS.GRANTED;
      }
    } catch (error) {
      console.error('Check permission error:', error);
      return false;
    }
  },
};

export default permissions;
