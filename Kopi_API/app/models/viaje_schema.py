from pydantic import BaseModel,field_validator
from typing import List, Optional
from datetime import date, time
import struct

class ParadaBase(BaseModel):
    nombre_parada: str
    coordenadas: str
    orden: int
    @field_validator('coordenadas', mode='before')
    @classmethod
    def decodificar_espacial(cls, valor):
        if isinstance(valor, bytes) and len(valor) >= 25:
            lon, lat = struct.unpack('<dd', valor[9:25])
            return f"{lat},{lon}"
        return valor

class ViajeCrear(BaseModel):
    vehiculo_id: int
    origen: str
    destino: str
    fecha_salida: date
    hora_salida: time
    asientos_disponibles: int
    costo_por_asiento: float
    paradas: List[ParadaBase] 

class ViajeActualizar(BaseModel):
    origen: Optional[str] = None
    destino: Optional[str] = None
    fecha_salida: Optional[date] = None
    hora_salida: Optional[time] = None
    asientos_disponibles: Optional[int] = None
    costo_por_asiento: Optional[float] = None
    estatus: Optional[str] = None

class ParadaRespuesta(ParadaBase):
    id: int
    class Config:
        from_attributes = True

# La respuesta completa de la API
class ViajeRespuesta(BaseModel):
    id: int
    conductor_id: int
    vehiculo_id: int
    origen: str
    destino: str
    fecha_salida: date
    hora_salida: time
    asientos_disponibles: int
    costo_por_asiento: float
    estatus: str
    paradas: List[ParadaRespuesta] = []

    class Config:
        from_attributes = True
