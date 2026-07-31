from fastapi import APIRouter, WebSocket, WebSocketDisconnect, Depends, Query, status
from sqlalchemy.orm import Session
from typing import Dict, List
from jose import jwt, JWTError

from app.data import database, models
from app.core.config import settings

router = APIRouter(prefix="/ws/gps", tags=["Rastreo GPS en Vivo"])

# --- GESTOR DE CONEXIONES ---
class ConnectionManager:
    def __init__(self):
        # Agrupa las conexiones por ID de viaje: { viaje_id: [websocket1, websocket2] }
        self.active_connections: Dict[int, List[WebSocket]] = {}

    async def connect(self, websocket: WebSocket, viaje_id: int):
        await websocket.accept()
        if viaje_id not in self.active_connections:
            self.active_connections[viaje_id] = []
        self.active_connections[viaje_id].append(websocket)

    def disconnect(self, websocket: WebSocket, viaje_id: int):
        if viaje_id in self.active_connections:
            self.active_connections[viaje_id].remove(websocket)
            if not self.active_connections[viaje_id]:
                del self.active_connections[viaje_id]

    async def broadcast(self, viaje_id: int, message: dict):
        if viaje_id in self.active_connections:
            for connection in self.active_connections[viaje_id]:
                await connection.send_json(message)

manager = ConnectionManager()

# --- AUTENTICACIÓN PARA WEBSOCKETS ---
# Los WebSockets de navegador no pueden enviar headers de Authorization fácilmente,
# así que leemos el token directamente desde los parámetros de la URL (?token=...)
def get_ws_user(token: str, db: Session):
    try:
        payload = jwt.decode(token, settings.KOPI_SECRET_KEY, algorithms=[settings.ALGORITHM if hasattr(settings, 'ALGORITHM') else "HS256"])
        user_id: str = payload.get("sub")
        if user_id is None:
            return None
        return db.query(models.Usuario).filter(models.Usuario.id == user_id).first()
    except JWTError:
        return None

# --- ENDPOINT PRINCIPAL ---
@router.websocket("/{viaje_id}")
async def gps_endpoint(websocket: WebSocket, viaje_id: int, token: str = Query(...), db: Session = Depends(database.get_db)):
    usuario = get_ws_user(token, db)
    if not usuario:
        await websocket.close(code=status.WS_1008_POLICY_VIOLATION)
        return

    # 🔥 FIX: Validar que el viaje exista Y ESTÉ ACTIVO
    viaje = db.query(models.Viaje).filter(
        models.Viaje.id == viaje_id, 
        models.Viaje.deleted_at.is_(None),
        models.Viaje.estatus.in_([models.EstatusViaje.programado, models.EstatusViaje.en_curso])
    ).first()
    
    if not viaje:
        await websocket.close(code=status.WS_1008_POLICY_VIOLATION)
        return

    es_conductor = (viaje.conductor_id == usuario.id)
    es_pasajero = db.query(models.Reservacion).filter(
        models.Reservacion.viaje_id == viaje_id,
        models.Reservacion.pasajero_id == usuario.id,
        models.Reservacion.estatus_reserva == models.EstatusReserva.aceptado
    ).first()

    if not es_conductor and not es_pasajero:
        await websocket.close(code=status.WS_1008_POLICY_VIOLATION)
        return

    await manager.connect(websocket, viaje_id)

    try:
        while True:
            data = await websocket.receive_json()
            if es_conductor:
                # 🔥 FIX: Validar coordenadas reales
                lat, lon = data.get("latitud"), data.get("longitud")
                if lat is not None and lon is not None:
                    await manager.broadcast(viaje_id, {
                        "tipo": "actualizacion_gps",
                        "viaje_id": viaje_id,
                        "latitud": float(lat),
                        "longitud": float(lon),
                        "timestamp": data.get("timestamp")
                    })
            else:
                await websocket.send_json({"error": "Solo el conductor puede emitir coordenadas."})
                
    except WebSocketDisconnect:
        manager.disconnect(websocket, viaje_id)
        # 🔥 FIX: Alerta de pérdida de señal del chofer
        if es_conductor:
            await manager.broadcast(viaje_id, {
                "tipo": "alerta_conexion",
                "mensaje": "⚠️ El conductor ha perdido la señal GPS o se ha desconectado."
            })