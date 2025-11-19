import AsyncStorage from '@react-native-async-storage/async-storage';

/**
 * AsyncStorage utilities
 */

export const storage = {
  /**
   * Save data to storage
   */
  save: async <T>(key: string, value: T): Promise<void> => {
    try {
      const jsonValue = JSON.stringify(value);
      await AsyncStorage.setItem(key, jsonValue);
    } catch (error) {
      console.error('Storage save error:', error);
      throw error;
    }
  },

  /**
   * Get data from storage
   */
  get: async <T>(key: string): Promise<T | null> => {
    try {
      const jsonValue = await AsyncStorage.getItem(key);
      return jsonValue != null ? JSON.parse(jsonValue) : null;
    } catch (error) {
      console.error('Storage get error:', error);
      return null;
    }
  },

  /**
   * Remove data from storage
   */
  remove: async (key: string): Promise<void> => {
    try {
      await AsyncStorage.removeItem(key);
    } catch (error) {
      console.error('Storage remove error:', error);
      throw error;
    }
  },

  /**
   * Clear all storage
   */
  clear: async (): Promise<void> => {
    try {
      await AsyncStorage.clear();
    } catch (error) {
      console.error('Storage clear error:', error);
      throw error;
    }
  },

  /**
   * Get all keys
   */
  getAllKeys: async (): Promise<string[]> => {
    try {
      return await AsyncStorage.getAllKeys();
    } catch (error) {
      console.error('Storage getAllKeys error:', error);
      return [];
    }
  },

  /**
   * Get multiple items
   */
  getMultiple: async <T>(keys: string[]): Promise<Record<string, T>> => {
    try {
      const values = await AsyncStorage.multiGet(keys);
      const result: Record<string, T> = {};
      values.forEach(([key, value]) => {
        if (value) {
          result[key] = JSON.parse(value);
        }
      });
      return result;
    } catch (error) {
      console.error('Storage getMultiple error:', error);
      return {};
    }
  },

  /**
   * Save multiple items
   */
  saveMultiple: async <T>(items: Record<string, T>): Promise<void> => {
    try {
      const pairs: [string, string][] = Object.entries(items).map(([key, value]) => [
        key,
        JSON.stringify(value),
      ]);
      await AsyncStorage.multiSet(pairs);
    } catch (error) {
      console.error('Storage saveMultiple error:', error);
      throw error;
    }
  },
};

// Pre-defined storage keys
export const STORAGE_KEYS = {
  AUTH_TOKEN: 'auth_token',
  USER: 'user',
  FAVORITES: 'favorites',
  SEARCH_HISTORY: 'search_history',
  THEME: 'theme',
  LANGUAGE: 'language',
  NOTIFICATIONS_ENABLED: 'notifications_enabled',
};

export default storage;
