from fastapi import HTTPException
from sqlalchemy.orm import Session
from app.data import models
from app.models import reserva_schema
from app.services.notificacion_service import NotificacionService
from datetime import datetime, timedelta

class ReservacionService:
    @staticmethod
    def listar(db: Session, usuario: models.Usuario, rol: str = "pasajero"):
        if rol == "conductor":
            return db.query(models.Reservacion)\
                .join(models.Viaje, models.Reservacion.viaje_id == models.Viaje.id)\
                .filter(
                    models.Viaje.conductor_id == usuario.id,
                    models.Viaje.deleted_at.is_(None)
                )\
                .all()
        else:
            return db.query(models.Reservacion)\
                .filter(models.Reservacion.pasajero_id == usuario.id)\
                .all()

    @staticmethod
    def crear(db: Session, request: reserva_schema.ReservacionCrear, usuario: models.Usuario):
        viaje = db.query(models.Viaje).filter(
            models.Viaje.id == request.viaje_id,
            models.Viaje.estatus == models.EstatusViaje.programado,
            models.Viaje.deleted_at.is_(None),
        ).with_for_update().first()

        if not viaje: raise HTTPException(status_code=404, detail="Viaje no encontrado o no disponible.")
        if viaje.conductor_id == usuario.id: raise HTTPException(status_code=422, detail="No puedes reservar tu propio viaje.")
        if request.asientos_solicitados > viaje.asientos_disponibles: raise HTTPException(status_code=422, detail="No hay suficientes asientos disponibles.")

        parada = db.query(models.ParadaViaje).filter(
            models.ParadaViaje.id == request.parada_subida_id, models.ParadaViaje.viaje_id == viaje.id
        ).first()
        if not parada: raise HTTPException(status_code=422, detail="La parada no pertenece al viaje seleccionado.")

        existente = db.query(models.Reservacion).filter(
            models.Reservacion.viaje_id == viaje.id,
            models.Reservacion.pasajero_id == usuario.id,
            models.Reservacion.estatus_reserva.in_([models.EstatusReserva.solicitado, models.EstatusReserva.aceptado]),
        ).first()
        if existente: raise HTTPException(status_code=422, detail="Ya tienes una reservación activa para este viaje.")

        reservacion = models.Reservacion(**request.model_dump(), pasajero_id=usuario.id)
        db.add(reservacion)
        db.commit()
        db.refresh(reservacion)
        return reservacion

    @staticmethod
    def actualizar_estatus(db: Session, reservacion_id: int, nuevo_estatus: str, conductor: models.Usuario):
        reserva = db.query(models.Reservacion).filter(models.Reservacion.id == reservacion_id).first()
        if not reserva:
            raise HTTPException(status_code=404, detail="Solicitud de viaje no encontrada.")
        
        viaje = db.query(models.Viaje).filter(models.Viaje.id == reserva.viaje_id).first()
        
        es_conductor = (viaje.conductor_id == conductor.id)
        es_pasajero = (reserva.pasajero_id == conductor.id)

        if not es_conductor and not es_pasajero:
            raise HTTPException(status_code=403, detail="No tienes permiso para administrar esta reservación.")

        if nuevo_estatus not in ["solicitado", "aceptado", "rechazado", "cancelado"]:
            raise HTTPException(status_code=422, detail="Estatus de reservación inválido.")

        estatus_anterior = getattr(reserva.estatus_reserva, "value", reserva.estatus_reserva)
        
        # LOGICA DE PENALIZACIÓN
        if nuevo_estatus == "cancelado" and estatus_anterior == "aceptado" and not es_conductor:
            # Si el PASAJERO cancela y faltan menos de 2 horas
            tiempo_faltante = datetime.combine(viaje.fecha_salida, viaje.hora_salida) - datetime.now()
            if tiempo_faltante < timedelta(hours=2):
                print(f"PENALIZACIÓN DEL 50% PARA EL USUARIO {conductor.id} APLICADA")
                
                # Crear registro de penalidad en la tabla pagos
                from decimal import Decimal
                costo_total = viaje.costo_por_asiento * reserva.asientos_solicitados
                penalizacion_monto = costo_total * Decimal("0.50")
                
                pago_penalidad = models.Pago(
                    reservacion_id=reserva.id,
                    monto=penalizacion_monto,
                    metodo_pago=models.MetodoPago.tarjeta,
                    estatus_pago=models.EstatusPago.pendiente
                )
                db.add(pago_penalidad)

        # 2. LÓGICA DE ASIGNACIÓN: Si pasa a 'aceptado', verificamos y restamos asientos
        if nuevo_estatus == "aceptado" and estatus_anterior != "aceptado":
            if viaje.asientos_disponibles < reserva.asientos_solicitados:
                raise HTTPException(
                    status_code=400, 
                    detail=f"⚠️ Sobrecupo: Solo quedan {viaje.asientos_disponibles} asiento(s) libre(s) en este auto."
                )
            viaje.asientos_disponibles -= reserva.asientos_solicitados

        # 3. LÓGICA DE DEVOLUCIÓN: Si cancela o rechaza a alguien que ya estaba 'aceptado', regresamos los asientos al auto
        elif estatus_anterior == "aceptado" and nuevo_estatus in ["rechazado", "cancelado"]:
            viaje.asientos_disponibles += reserva.asientos_solicitados

        reserva.estatus_reserva = nuevo_estatus
        db.commit()
        db.refresh(reserva)

        return reserva
