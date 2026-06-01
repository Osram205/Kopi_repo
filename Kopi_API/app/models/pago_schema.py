from datetime import datetime
from typing import Optional

from pydantic import BaseModel


class PagoCrear(BaseModel):
    reservacion_id: int
    metodo_pago: str


class PagoEstatus(BaseModel):
    estatus_pago: str


class PagoRespuesta(BaseModel):
    id: int
    reservacion_id: int
    monto: float
    metodo_pago: str
    estatus_pago: str
    fecha_pago: Optional[datetime] = None

    class Config:
        from_attributes = True
