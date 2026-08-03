/**
 * Persistencia del JWT usando expo-secure-store.
 *
 * Por qué SecureStore y no AsyncStorage:
 * - AsyncStorage guarda en texto plano (SharedPreferences / plist sin cifrar).
 * - SecureStore usa Keychain en iOS y Keystore/EncryptedSharedPreferences en
 *   Android, que es el mecanismo recomendado por Expo para tokens y secretos.
 * - Es la opción oficial sugerida en la documentación de Expo SDK 57 para
 *   "storing sensitive data" (credenciales, tokens de sesión).
 *
 * Limitación conocida: SecureStore no funciona en Expo Web. Como Kopi_App
 * es una app exclusivamente móvil (Conductor/Pasajero), esto no representa
 * un problema para este proyecto.
 */

import * as SecureStore from 'expo-secure-store';

const TOKEN_KEY = 'kopi_access_token';

export const tokenStorage = {
  async getToken(): Promise<string | null> {
    try {
      return await SecureStore.getItemAsync(TOKEN_KEY);
    } catch (error) {
      console.warn('[Kopi] No se pudo leer el token almacenado:', error);
      return null;
    }
  },

  async setToken(token: string): Promise<void> {
    await SecureStore.setItemAsync(TOKEN_KEY, token);
  },

  async removeToken(): Promise<void> {
    try {
      await SecureStore.deleteItemAsync(TOKEN_KEY);
    } catch (error) {
      console.warn('[Kopi] No se pudo eliminar el token almacenado:', error);
    }
  },
};
