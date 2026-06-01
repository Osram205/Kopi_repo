from typing import Optional

from pydantic import BaseModel


class CalificacionCrear(BaseModel):
    viaje_id: int
    evaluado_id: int
    puntuacion: int
    comentarios: Optional[str] = None


class CalificacionRespuesta(BaseModel):
    id: int
    viaje_id: int
    evaluador_id: int
    evaluado_id: int
    rol_evaluador: str
    puntuacion: int
    comentarios: Optional[str] = None

    class Config:
        from_attributes = True
