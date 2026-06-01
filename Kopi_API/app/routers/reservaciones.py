from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session

from app.data import database, models
from app.models import reserva_schema
from app.security.oauth2 import get_current_user

router = APIRouter(prefix="/reservaciones", tags=["Reservaciones"])


@router.get("/", response_model=list[reserva_schema.ReservacionRespuesta])
def listar_reservaciones(
    db: Session = Depends(database.get_db),
    usuario: models.Usuario = Depends(get_current_user),
):
    return db.query(models.Reservacion).join(models.Viaje).filter(
        (models.Reservacion.pasajero_id == usuario.id) | (models.Viaje.conductor_id == usuario.id)
    ).order_by(models.Reservacion.created_at.desc()).all()


@router.post("/", response_model=reserva_schema.ReservacionRespuesta, status_code=status.HTTP_201_CREATED)
def crear_reservacion(
    request: reserva_schema.ReservacionCrear,
    db: Session = Depends(database.get_db),
    usuario: models.Usuario = Depends(get_current_user),
):
    viaje = db.query(models.Viaje).filter(
        models.Viaje.id == request.viaje_id,
        models.Viaje.estatus == models.EstatusViaje.programado,
        models.Viaje.deleted_at.is_(None),
    ).with_for_update().first()

    if not viaje:
        raise HTTPException(status_code=404, detail="Viaje no encontrado o no disponible.")
    if viaje.conductor_id == usuario.id:
        raise HTTPException(status_code=422, detail="No puedes reservar tu propio viaje.")
    if request.asientos_solicitados > viaje.asientos_disponibles:
        raise HTTPException(status_code=422, detail="No hay suficientes asientos disponibles.")

    parada = db.query(models.ParadaViaje).filter(
        models.ParadaViaje.id == request.parada_subida_id,
        models.ParadaViaje.viaje_id == viaje.id,
    ).first()
    if not parada:
        raise HTTPException(status_code=422, detail="La parada no pertenece al viaje seleccionado.")

    existente = db.query(models.Reservacion).filter(
        models.Reservacion.viaje_id == viaje.id,
        models.Reservacion.pasajero_id == usuario.id,
        models.Reservacion.estatus_reserva.in_([models.EstatusReserva.solicitado, models.EstatusReserva.aceptado]),
    ).first()
    if existente:
        raise HTTPException(status_code=422, detail="Ya tienes una reservación activa para este viaje.")

    reservacion = models.Reservacion(**request.model_dump(), pasajero_id=usuario.id)
    db.add(reservacion)
    db.commit()
    db.refresh(reservacion)
    return reservacion


@router.put("/{reservacion_id}/estatus", response_model=reserva_schema.ReservacionRespuesta)
def actualizar_estatus(
    reservacion_id: int,
    request: reserva_schema.ReservacionEstatus,
    db: Session = Depends(database.get_db),
    usuario: models.Usuario = Depends(get_current_user),
):
    estatus_permitidos = {"aceptado", "rechazado", "cancelado"}
    if request.estatus_reserva not in estatus_permitidos:
        raise HTTPException(status_code=422, detail="Estatus de reservación inválido.")

    reservacion = db.query(models.Reservacion).filter(models.Reservacion.id == reservacion_id).with_for_update().first()
    if not reservacion:
        raise HTTPException(status_code=404, detail="Reservación no encontrada.")

    viaje = db.query(models.Viaje).filter(models.Viaje.id == reservacion.viaje_id).with_for_update().first()
    if viaje.conductor_id != usuario.id and reservacion.pasajero_id != usuario.id:
        raise HTTPException(status_code=403, detail="No autorizado.")
    if request.estatus_reserva != "cancelado" and viaje.conductor_id != usuario.id:
        raise HTTPException(status_code=403, detail="Solo el conductor puede aceptar o rechazar reservaciones.")

    if request.estatus_reserva == "aceptado" and reservacion.estatus_reserva != models.EstatusReserva.aceptado:
        if reservacion.asientos_solicitados > viaje.asientos_disponibles:
            raise HTTPException(status_code=422, detail="No hay suficientes asientos disponibles.")
        viaje.asientos_disponibles -= reservacion.asientos_solicitados

    if request.estatus_reserva == "cancelado" and reservacion.estatus_reserva == models.EstatusReserva.aceptado:
        viaje.asientos_disponibles += reservacion.asientos_solicitados

    reservacion.estatus_reserva = models.EstatusReserva(request.estatus_reserva)
    db.commit()
    db.refresh(reservacion)
    return reservacion
