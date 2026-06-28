from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from app.data import database, models
from app.models import vehiculos_schema
from app.security import oauth2
from app.security.oauth2 import get_current_user
from app.services.vehiculo_service import VehiculoService

router = APIRouter(prefix="/vehiculos", tags=["Vehículos"])

@router.get("/", response_model=list[vehiculos_schema.VehiculoRespuesta])
def listar_vehiculos(db: Session = Depends(database.get_db), usuario: models.Usuario = Depends(get_current_user)):
    return VehiculoService.listar(db, usuario)

@router.post("/", status_code=status.HTTP_201_CREATED)
def registrar_vehiculo(
    vehiculo_data: vehiculos_schema.VehiculoCrear, # Tu esquema de validación de entrada
    db: Session = Depends(database.get_db),
    usuario_actual: models.Usuario = Depends(oauth2.get_current_user)
):
    # 🛑 CANDADO DE SEGURIDAD CRÍTICO
    if usuario_actual.estatus_verificacion != "aprobado":
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Acceso denegado. Tu cuenta no ha sido aprobada por el administrador para ser conductor."
        )

    # Si pasa el candado, procedemos a guardar el vehículo en MySQL
    nuevo_vehiculo = models.Vehiculo(
        conductor_id=usuario_actual.id,
        placas=vehiculo_data.placas,
        marca=vehiculo_data.marca,
        modelo=vehiculo_data.modelo,
        color=vehiculo_data.color,
        asientos_totales=vehiculo_data.asientos_totales
    )
    
    # Al registrar su primer vehículo, también nos aseguramos de encender su bandera de conductor
    usuario_actual.es_conductor = True
    
    db.add(nuevo_vehiculo)
    db.commit()
    db.refresh(nuevo_vehiculo)
    
    return {"mensaje": "Vehículo registrado con éxito y rol de conductor activado.", "vehiculo_id": nuevo_vehiculo.id}

@router.put("/{vehiculo_id}", response_model=vehiculos_schema.VehiculoRespuesta)
def actualizar_vehiculo(vehiculo_id: int, request: vehiculos_schema.VehiculoCrear, db: Session = Depends(database.get_db), usuario: models.Usuario = Depends(get_current_user)):
    return VehiculoService.actualizar(db, vehiculo_id, request, usuario)

@router.delete("/{vehiculo_id}")
def eliminar_vehiculo(vehiculo_id: int, db: Session = Depends(database.get_db), usuario: models.Usuario = Depends(get_current_user)):
    return VehiculoService.eliminar(db, vehiculo_id, usuario)