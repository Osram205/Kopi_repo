from pydantic import BaseModel
from datetime import datetime
from typing import Optional

class PasajeroBasico(BaseModel):
    id: int
    nombre: str
    foto_perfil: Optional[str] = None
    foto_credencial_frente: Optional[str] = None
    foto_credencial_trasera: Optional[str] = None
    correo_institucional: str

    class Config:
        from_attributes = True

class ViajeBasico(BaseModel):
    id: int
    origen: str
    destino: str
    costo_por_asiento: float

    class Config:
        from_attributes = True

class ReservacionCrear(BaseModel):
    viaje_id: int
    parada_subida_id: int
    asientos_solicitados: int = 1


class ReservacionEstatus(BaseModel):
    estatus_reserva: str


class ReservacionRespuesta(BaseModel):
    id: int
    viaje_id: int
    pasajero_id: int
    parada_subida_id: int
    asientos_solicitados: int
    estatus_reserva: str
    created_at: datetime
    pasajero: Optional[PasajeroBasico] = None
    viaje: Optional[ViajeBasico] = None

    class Config:
        from_attributes = True
