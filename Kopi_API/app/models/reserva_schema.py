from pydantic import BaseModel
from datetime import datetime


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

    class Config:
        from_attributes = True
