/**
 * Contexto global de autenticación.
 *
 * Envuelve toda la app (ver App.tsx) y expone:
 * - usuario / isAuthenticated / isLoading
 * - login(credenciales)
 * - logout()
 * - refrescarPerfil()
 *
 * RootNavigator.tsx consume `isAuthenticated` e `isLoading` para decidir
 * qué mostrar: pantalla de carga, Login, o la app principal (Main).
 * Esa es la "protección de rutas" de este módulo: mientras no haya
 * `usuario`, físicamente no existe ninguna ruta de Main montada en el
 * stack de navegación.
 */

import React, {
  createContext,
  ReactNode,
  useCallback,
  useContext,
  useEffect,
  useState,
} from 'react';

import { AuthService } from '../services/auth.service';
import { LoginRequest, UsuarioPerfil } from '../types/auth.types';
import { registerForPushNotificationsAsync } from '../services/notification.service';

interface AuthContextValue {
  /** Perfil del usuario autenticado, o null si no hay sesión. */
  usuario: UsuarioPerfil | null;
  /** true mientras se restaura la sesión al abrir la app. */
  isLoading: boolean;
  /** Atajo derivado de `usuario !== null`. */
  isAuthenticated: boolean;
  /** Ejecuta login contra /auth/login y guarda la sesión. */
  login: (credentials: LoginRequest) => Promise<void>;
  /** Cierra sesión y limpia el token persistido. */
  logout: () => Promise<void>;
  /** Vuelve a pedir /usuarios/perfil (útil tras editar el perfil). */
  refrescarPerfil: () => Promise<void>;
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [usuario, setUsuario] = useState<UsuarioPerfil | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  // Restauración automática de sesión al abrir la aplicación.
  useEffect(() => {
    let estaMontado = true;

    (async () => {
      const perfilRestaurado = await AuthService.restaurarSesion();
      if (estaMontado) {
        setUsuario(perfilRestaurado);
        setIsLoading(false);
      }
    })();

    return () => {
      estaMontado = false;
    };
  }, []);

  // Registrar notificaciones cuando el usuario inicia sesión o se restaura exitosamente
  useEffect(() => {
    if (usuario) {
      registerForPushNotificationsAsync().catch(console.error);
    }
  }, [usuario]);

  const login = useCallback(async (credentials: LoginRequest) => {
    const perfil = await AuthService.iniciarSesionCompleto(credentials);
    setUsuario(perfil);
  }, []);

  const logout = useCallback(async () => {
    await AuthService.cerrarSesion();
    setUsuario(null);
  }, []);

  const refrescarPerfil = useCallback(async () => {
    const perfil = await AuthService.obtenerPerfil();
    setUsuario(perfil);
  }, []);

  return (
    <AuthContext.Provider
      value={{
        usuario,
        isLoading,
        isAuthenticated: usuario !== null,
        login,
        logout,
        refrescarPerfil,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
}

/** Hook para consumir el contexto. Lanza un error claro si se usa fuera del provider. */
export function useAuth(): AuthContextValue {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth() debe usarse dentro de un <AuthProvider>.');
  }
  return context;
}
