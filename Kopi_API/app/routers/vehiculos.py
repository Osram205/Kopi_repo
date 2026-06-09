from fastapi import APIRouter, Depends, status
from sqlalchemy.orm import Session
from app.data import database, models
from app.models import vehiculos_schema
from app.security.oauth2 import get_current_user
from app.services.vehiculo_service import VehiculoService

router = APIRouter(prefix="/vehiculos", tags=["Vehículos"])

@router.get("/", response_model=list[vehiculos_schema.VehiculoRespuesta])
def listar_vehiculos(db: Session = Depends(database.get_db), usuario: models.Usuario = Depends(get_current_user)):
    return VehiculoService.listar(db, usuario)

@router.post("/", response_model=vehiculos_schema.VehiculoRespuesta, status_code=status.HTTP_201_CREATED)
def crear_vehiculo(request: vehiculos_schema.VehiculoCrear, db: Session = Depends(database.get_db), usuario: models.Usuario = Depends(get_current_user)):
    return VehiculoService.crear(db, request, usuario)

@router.put("/{vehiculo_id}", response_model=vehiculos_schema.VehiculoRespuesta)
def actualizar_vehiculo(vehiculo_id: int, request: vehiculos_schema.VehiculoCrear, db: Session = Depends(database.get_db), usuario: models.Usuario = Depends(get_current_user)):
    return VehiculoService.actualizar(db, vehiculo_id, request, usuario)

@router.delete("/{vehiculo_id}")
def eliminar_vehiculo(vehiculo_id: int, db: Session = Depends(database.get_db), usuario: models.Usuario = Depends(get_current_user)):
    return VehiculoService.eliminar(db, vehiculo_id, usuario)