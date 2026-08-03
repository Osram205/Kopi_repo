/**
 * Tipos correspondientes a los schemas de Pydantic definidos en
 * Kopi_API (app/models/auth_schema.py) y al endpoint GET /usuarios/perfil
 * (app/routers/usuarios.py). Se mantienen los nombres de campo EXACTOS
 * que devuelve el backend (snake_case) para no tener que mapear nada.
 */

/** Body que espera POST /auth/login (enviado como x-www-form-urlencoded). */
export interface LoginRequest {
  correo_institucional: string;
  contrasena: string;
}

/** Respuesta de POST /auth/login. */
export interface TokenResponse {
  access_token: string;
  token_type: string;
}

/**
 * Respuesta de GET /usuarios/perfil.
 * OJO: este endpoint devuelve un dict armado a mano en usuarios.py,
 * no el schema `UsuarioRespuesta` de auth_schema.py, por lo que
 * NO incluye `apellidos` como obligatorio ni `created_at`.
 */
export interface UsuarioPerfil {
  id: number;
  nombre: string;
  apellidos: string | null;
  carrera: string | null;
  matricula: string;
  foto_perfil: string | null;
  correo_institucional: string;
  telefono: string;
  estatus_verificacion: 'pendiente' | 'solicitado' | 'aprobado' | 'rechazado' | string;
  es_conductor: boolean;
}

/**
 * FastAPI devuelve errores de dos formas distintas:
 * 1) HTTPException manual -> { "detail": "mensaje en texto plano" }
 * 2) Error de validación de Pydantic (422) ->
 *    { "detail": [{ "loc": [...], "msg": "mensaje", "type": "..." }] }
 */
export interface ApiValidationErrorItem {
  loc?: (string | number)[];
  msg: string;
  type?: string;
}

export type ApiErrorDetail = string | ApiValidationErrorItem[];

export interface ApiErrorResponse {
  detail: ApiErrorDetail;
}
