/**
 * Configuración de entorno de Kopi_App.
 *
 * Expo (SDK 49+) inyecta automáticamente en `process.env` cualquier variable
 * de un archivo `.env` que empiece con el prefijo `EXPO_PUBLIC_`. No se
 * necesita ninguna librería adicional (ni `expo-constants`, ni `dotenv`).
 *
 * Ver `.env.example` en la raíz del proyecto para el valor esperado.
 */

const RAW_API_URL = process.env.EXPO_PUBLIC_API_URL;

if (!RAW_API_URL) {
  // No lanzamos una excepción para no tumbar la app en desarrollo,
  // pero dejamos muy claro en consola que falta configuración.
  console.warn(
    '[Kopi] EXPO_PUBLIC_API_URL no está definida.\n' +
      'Crea un archivo ".env" en la raíz de Kopi_App (copia ".env.example") ' +
      'y reinicia el servidor de Expo con "npx expo start -c".'
  );
}

/**
 * URL base de Kopi_API (FastAPI). Incluye protocolo y puerto,
 * sin slash final (ej. "http://192.168.1.15:8000").
 */
export const API_BASE_URL: string = RAW_API_URL ?? 'http://127.0.0.1:8000';

/** Tiempo máximo de espera para cualquier petición HTTP, en milisegundos. */
export const API_TIMEOUT_MS = 15000;
