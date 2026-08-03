/**
 * Cliente HTTP central de Kopi_App.
 *
 * Todas las llamadas a Kopi_API (FastAPI) deben pasar por esta instancia
 * de axios, nunca por `fetch` directo, para garantizar que:
 * 1) Siempre apunten a la misma `baseURL` configurada por entorno.
 * 2) El Bearer Token se adjunte automáticamente cuando exista sesión.
 */

import axios from 'axios';
import { API_BASE_URL, API_TIMEOUT_MS } from '../config/env';
import { tokenStorage } from '../services/tokenStorage';

export const apiClient = axios.create({
  baseURL: API_BASE_URL,
  timeout: API_TIMEOUT_MS,
});

/**
 * Interceptor de request: adjunta "Authorization: Bearer <token>"
 * en cada petición saliente si hay un token guardado en SecureStore.
 * Esto reproduce, del lado del cliente, exactamente lo que Kopi_Web
 * hace con `Http::withToken($token)` en AuthController.php.
 */
apiClient.interceptors.request.use(async (config) => {
  const token = await tokenStorage.getToken();

  if (token) {
    config.headers = config.headers ?? {};
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});
