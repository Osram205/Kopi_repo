from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session

from app.data import database, models
from app.models import calificacion_schema
from app.security.oauth2 import get_current_user

router = APIRouter(prefix="/calificaciones", tags=["Calificaciones"])


@router.post("/", response_model=calificacion_schema.CalificacionRespuesta, status_code=status.HTTP_201_CREATED)
def crear_calificacion(
    request: calificacion_schema.CalificacionCrear,
    db: Session = Depends(database.get_db),
    usuario: models.Usuario = Depends(get_current_user),
):
    if not 1 <= request.puntuacion <= 5:
        raise HTTPException(status_code=422, detail="La puntuación debe estar entre 1 y 5.")
    if request.evaluado_id == usuario.id:
        raise HTTPException(status_code=422, detail="No puedes calificarte a ti mismo.")

    viaje = db.query(models.Viaje).filter(models.Viaje.id == request.viaje_id).first()
    if not viaje:
        raise HTTPException(status_code=404, detail="Viaje no encontrado.")

    es_conductor = viaje.conductor_id == usuario.id
    es_pasajero = db.query(models.Reservacion).filter(
        models.Reservacion.viaje_id == viaje.id,
        models.Reservacion.pasajero_id == usuario.id,
        models.Reservacion.estatus_reserva == models.EstatusReserva.aceptado,
    ).first() is not None

    if not es_conductor and not es_pasajero:
        raise HTTPException(status_code=403, detail="Solo participantes del viaje pueden calificar.")

    calificacion = models.Calificacion(
        **request.model_dump(),
        evaluador_id=usuario.id,
        rol_evaluador=models.RolEvaluador.conductor if es_conductor else models.RolEvaluador.pasajero,
    )

    db.add(calificacion)
    db.commit()
    db.refresh(calificacion)
    return calificacion
