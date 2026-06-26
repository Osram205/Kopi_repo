import firebase_admin
from firebase_admin import credentials, messaging
import os

# 1. Inicializar la conexión con Firebase
# Nota: Debes descargar tu archivo JSON de credenciales desde la consola de Firebase 
# (Configuración del proyecto > Cuentas de servicio) y guardarlo en la raíz de Kopi_API
ruta_credenciales = os.getenv("FIREBASE_CREDENTIALS", "firebase-adminsdk.json")

if not firebase_admin._apps:
    try:
        cred = credentials.Certificate(ruta_credenciales)
        firebase_admin.initialize_app(cred)
    except Exception as e:
        print(f"Advertencia: No se pudo inicializar Firebase. Faltan credenciales: {e}")

class NotificacionService:
    @staticmethod
    def notificar_reserva_aceptada(token_dispositivo: str, conductor_nombre: str):
        if not token_dispositivo:
            return False # El pasajero no ha registrado su teléfono
        
        try:
            mensaje = messaging.Message(
                notification=messaging.Notification(
                    title="¡Viaje Confirmado! 🚗",
                    body=f"{conductor_nombre} ha aceptado tu solicitud de viaje. Prepárate en el punto de encuentro.",
                ),
                data={"tipo": "reserva_aceptada"},
                token=token_dispositivo,
            )
            # Enviar a Firebase
            respuesta = messaging.send(mensaje)
            return True
        except Exception as e:
            print(f"Error al enviar la notificación Push: {e}")
            return False