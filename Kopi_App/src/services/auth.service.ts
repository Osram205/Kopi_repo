/**
 * Servicio de autenticación.
 *
 * Consume ÚNICAMENTE endpoints que ya existen en Kopi_API:
 * - POST /auth/login          (app/routers/auth.py)
 * - GET  /usuarios/perfil     (app/routers/usuarios.py)
 *
 * No se inventa ningún endpoint nuevo.
 */

import { AxiosError } from 'axios';
import { apiClient } from '../api/client';
import { tokenStorage } from './tokenStorage';
import {
  ApiErrorResponse,
  LoginRequest,
  TokenResponse,
  UsuarioPerfil,
} from '../types/auth.types';

/**
 * Convierte cualquier error de axios/FastAPI en un mensaje de texto
 * legible para mostrar en pantalla. FastAPI puede devolver:
 * - 401/400 con { detail: "mensaje" }               (credenciales inválidas, etc.)
 * - 422     con { detail: [{ msg: "mensaje" }, ...] } (error de validación de Pydantic)
 * - Sin response en absoluto (timeout / sin conexión / servidor caído)
 */
export function extractApiErrorMessage(
  error: unknown,
  fallback = 'Ocurrió un error inesperado. Intenta de nuevo.'
): string {
  if (error instanceof AxiosError) {
    if (!error.response) {
      if (error.code === 'ECONNABORTED') {
        return 'El servidor de Kopi tardó demasiado en responder. Intenta de nuevo.';
      }
      return 'No se pudo conectar con el servidor de Kopi. Revisa tu conexión e inténtalo de nuevo.';
    }

    const data = error.response.data as ApiErrorResponse | undefined;
    const detail = data?.detail;

    if (typeof detail === 'string' && detail.trim().length > 0) {
      return detail;
    }

    if (Array.isArray(detail) && detail.length > 0 && detail[0]?.msg) {
      return detail[0].msg;
    }
  }

  return fallback;
}

export const AuthService = {
  /**
   * Llama a POST /auth/login.
   *
   * El backend usa `OAuth2PasswordRequestForm`, que exige el body en
   * formato x-www-form-urlencoded con las llaves fijas "username" y
   * "password" (no "correo_institucional"/"contrasena"). Por eso se
   * traduce aquí antes de enviarlo, tal como hace Kopi_Web en
   * AuthController::procesarLogin con Http::asForm().
   */
  async login(credentials: LoginRequest): Promise<TokenResponse> {
    const body = new URLSearchParams();
    body.append('username', credentials.correo_institucional);
    body.append('password', credentials.contrasena);

    const { data } = await apiClient.post<TokenResponse>('/auth/login', body.toString(), {
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    });

    return data;
  },

  /** Llama a GET /usuarios/perfil (requiere Bearer token, lo pone el interceptor). */
  async obtenerPerfil(): Promise<UsuarioPerfil> {
    const { data } = await apiClient.get<UsuarioPerfil>('/usuarios/perfil');
    return data;
  },

  /**
   * Flujo completo de login: autentica, guarda el token y obtiene el
   * perfil. Si obtener el perfil falla después de un login exitoso
   * (caso raro, pero posible), se revierte guardando "sesión limpia"
   * para no dejar un token huérfano sin datos de usuario.
   */
  async iniciarSesionCompleto(credentials: LoginRequest): Promise<UsuarioPerfil> {
    const { access_token } = await this.login(credentials);
    await tokenStorage.setToken(access_token);

    try {
      return await this.obtenerPerfil();
    } catch (error) {
      await tokenStorage.removeToken();
      throw error;
    }
  },

  /** Cierre de sesión: solo hay que borrar el token local (no hay endpoint de logout en el backend). */
  async cerrarSesion(): Promise<void> {
    await tokenStorage.removeToken();
  },

  /**
   * Restauración automática de sesión al abrir la app.
   * Si hay un token guardado, valida que siga siendo aceptado por el
   * backend pidiendo el perfil; si el token expiró o es inválido,
   * lo limpia y devuelve null (equivale a "no hay sesión").
   */
  async restaurarSesion(): Promise<UsuarioPerfil | null> {
    const token = await tokenStorage.getToken();
    if (!token) return null;

    try {
      return await this.obtenerPerfil();
    } catch {
      await tokenStorage.removeToken();
      return null;
    }
  },
};
