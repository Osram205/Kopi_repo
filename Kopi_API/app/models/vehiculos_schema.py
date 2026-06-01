from pydantic import BaseModel
from datetime import datetime

# Lo que el usuario envía para registrar su auto
class VehiculoCrear(BaseModel):
    placas: str
    marca: str
    modelo: str
    color: str
    asientos_totales: int

# Lo que la API devuelve (incluyendo el ID generado)
class VehiculoRespuesta(VehiculoCrear):
    id: int
    conductor_id: int
    created_at: datetime

    class Config:
        from_attributes = True