from fastapi import HTTPException
from sqlalchemy.orm import Session
from app.data import models
from app.models import calificacion_schema

class CalificacionService:
    @staticmethod
    def crear(db: Session, request: calificacion_schema.CalificacionCrear, usuario: models.Usuario):
        if not 1 <= request.puntuacion <= 5: 
            raise HTTPException(status_code=422, detail="La puntuación debe estar entre 1 y 5.")
        if request.evaluado_id == usuario.id: 
            raise HTTPException(status_code=422, detail="No puedes calificarte a ti mismo.")

        viaje = db.query(models.Viaje).filter(models.Viaje.id == request.viaje_id).first()
        if not viaje: 
            raise HTTPException(status_code=404, detail="Viaje no encontrado.")

        # 🔥 CANDADO 1: Solo se puede calificar si el viaje ya concluyó
        if viaje.estatus != models.EstatusViaje.completado:
            raise HTTPException(status_code=400, detail="El viaje debe haber finalizado para poder calificar.")

        # 🔥 CANDADO 2: Evitar calificaciones duplicadas (Spam)
        calificacion_previa = db.query(models.Calificacion).filter(
            models.Calificacion.viaje_id == request.viaje_id,
            models.Calificacion.evaluador_id == usuario.id,
            models.Calificacion.evaluado_id == request.evaluado_id
        ).first()
        
        if calificacion_previa:
            raise HTTPException(status_code=422, detail="Ya has enviado una calificación para este usuario en este viaje.")

        es_conductor = (viaje.conductor_id == usuario.id)
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