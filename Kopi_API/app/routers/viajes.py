from fastapi import APIRouter, Depends, Query, status
from sqlalchemy.orm import Session

from app.data import database, models
from app.models import viaje_schema
from app.security.oauth2 import get_current_user

# <-- IMPORTACIÓN NUEVA
from app.services.viaje_service import ViajeService 

router = APIRouter(prefix="/viajes", tags=["Viajes"])

@router.get("/", response_model=list[viaje_schema.ViajeRespuesta])
def listar_viajes(
    origen: str | None = Query(default=None),
    destino: str | None = Query(default=None),
    fecha_salida: str | None = Query(default=None),
    db: Session = Depends(database.get_db),
):
    return ViajeService.listar(db, origen, destino, fecha_salida)

@router.get("/{viaje_id}", response_model=viaje_schema.ViajeRespuesta)
def obtener_viaje(viaje_id: int, db: Session = Depends(database.get_db)):
    return ViajeService.obtener(db, viaje_id)

@router.post("/", response_model=viaje_schema.ViajeRespuesta, status_code=status.HTTP_201_CREATED)
def crear_viaje(
    request: viaje_schema.ViajeCrear,
    db: Session = Depends(database.get_db),
    usuario: models.Usuario = Depends(get_current_user),
):
    return ViajeService.crear(db, request, usuario)

@router.put("/{viaje_id}", response_model=viaje_schema.ViajeRespuesta)
def actualizar_viaje(
    viaje_id: int,
    request: viaje_schema.ViajeActualizar,
    db: Session = Depends(database.get_db),
    usuario: models.Usuario = Depends(get_current_user),
):
    return ViajeService.actualizar(db, viaje_id, request, usuario)

@router.delete("/{viaje_id}")
def cancelar_viaje(
    viaje_id: int,
    db: Session = Depends(database.get_db),
    usuario: models.Usuario = Depends(get_current_user),
):
    return ViajeService.cancelar(db, viaje_id, usuario)