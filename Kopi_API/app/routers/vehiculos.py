from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session

from app.data import database, models
from app.models import vehiculos_schema
from app.security.oauth2 import get_current_user

router = APIRouter(prefix="/vehiculos", tags=["Vehículos"])


@router.get("/", response_model=list[vehiculos_schema.VehiculoRespuesta])
def listar_vehiculos(
    db: Session = Depends(database.get_db),
    usuario: models.Usuario = Depends(get_current_user),
):
    return db.query(models.Vehiculo).filter(
        models.Vehiculo.conductor_id == usuario.id,
        models.Vehiculo.deleted_at.is_(None),
    ).all()


@router.post("/", response_model=vehiculos_schema.VehiculoRespuesta, status_code=status.HTTP_201_CREATED)
def crear_vehiculo(
    request: vehiculos_schema.VehiculoCrear,
    db: Session = Depends(database.get_db),
    usuario: models.Usuario = Depends(get_current_user),
):
    existente = db.query(models.Vehiculo).filter(models.Vehiculo.placas == request.placas).first()
    if existente:
        raise HTTPException(status_code=400, detail="Las placas ya están registradas.")

    vehiculo = models.Vehiculo(**request.model_dump(), conductor_id=usuario.id)
    usuario.es_conductor = True

    db.add(vehiculo)
    db.commit()
    db.refresh(vehiculo)
    return vehiculo


@router.put("/{vehiculo_id}", response_model=vehiculos_schema.VehiculoRespuesta)
def actualizar_vehiculo(
    vehiculo_id: int,
    request: vehiculos_schema.VehiculoCrear,
    db: Session = Depends(database.get_db),
    usuario: models.Usuario = Depends(get_current_user),
):
    vehiculo = db.query(models.Vehiculo).filter(
        models.Vehiculo.id == vehiculo_id,
        models.Vehiculo.conductor_id == usuario.id,
        models.Vehiculo.deleted_at.is_(None),
    ).first()

    if not vehiculo:
        raise HTTPException(status_code=404, detail="Vehículo no encontrado.")

    placas_existentes = db.query(models.Vehiculo).filter(
        models.Vehiculo.placas == request.placas,
        models.Vehiculo.id != vehiculo.id,
    ).first()
    if placas_existentes:
        raise HTTPException(status_code=400, detail="Las placas ya están registradas.")

    for field, value in request.model_dump().items():
        setattr(vehiculo, field, value)

    db.commit()
    db.refresh(vehiculo)
    return vehiculo


@router.delete("/{vehiculo_id}")
def eliminar_vehiculo(
    vehiculo_id: int,
    db: Session = Depends(database.get_db),
    usuario: models.Usuario = Depends(get_current_user),
):
    vehiculo = db.query(models.Vehiculo).filter(
        models.Vehiculo.id == vehiculo_id,
        models.Vehiculo.conductor_id == usuario.id,
        models.Vehiculo.deleted_at.is_(None),
    ).first()

    if not vehiculo:
        raise HTTPException(status_code=404, detail="Vehículo no encontrado.")

    from datetime import datetime

    vehiculo.deleted_at = datetime.utcnow()
    db.commit()
    return {"mensaje": "Vehículo eliminado correctamente."}
