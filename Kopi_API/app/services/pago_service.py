from datetime import datetime
from fastapi import HTTPException
from sqlalchemy.orm import Session
from app.data import models
from app.models import pago_schema

class PagoService:
    @staticmethod
    def crear(db: Session, request: pago_schema.PagoCrear, usuario: models.Usuario):
        if request.metodo_pago not in {"tarjeta", "transferencia"}: raise HTTPException(status_code=422, detail="Método de pago inválido.")
        
        reservacion = db.query(models.Reservacion).filter(models.Reservacion.id == request.reservacion_id).first()
        if not reservacion: raise HTTPException(status_code=404, detail="Reservación no encontrada.")
        if reservacion.pasajero_id != usuario.id: raise HTTPException(status_code=403, detail="Solo el pasajero puede registrar el pago.")
        if reservacion.estatus_reserva != models.EstatusReserva.aceptado: raise HTTPException(status_code=422, detail="La reservación debe estar aceptada para registrar pago.")

        existente = db.query(models.Pago).filter(models.Pago.reservacion_id == reservacion.id).first()
        if existente: raise HTTPException(status_code=422, detail="La reservación ya tiene un pago registrado.")

        viaje = db.query(models.Viaje).filter(models.Viaje.id == reservacion.viaje_id).first()
        pago = models.Pago(
            reservacion_id=reservacion.id,
            monto=reservacion.asientos_solicitados * viaje.costo_por_asiento,
            metodo_pago=models.MetodoPago(request.metodo_pago),
        )
        db.add(pago)
        db.commit()
        db.refresh(pago)
        return pago

    @staticmethod
    def actualizar_estatus(db: Session, pago_id: int, request: pago_schema.PagoEstatus, usuario: models.Usuario):
        if request.estatus_pago not in {"pendiente", "completado", "reembolsado"}: raise HTTPException(status_code=422, detail="Estatus de pago inválido.")

        pago = db.query(models.Pago).filter(models.Pago.id == pago_id).first()
        if not pago: raise HTTPException(status_code=404, detail="Pago no encontrado.")

        reservacion = db.query(models.Reservacion).filter(models.Reservacion.id == pago.reservacion_id).first()
        viaje = db.query(models.Viaje).filter(models.Viaje.id == reservacion.viaje_id).first()
        if viaje.conductor_id != usuario.id and reservacion.pasajero_id != usuario.id: raise HTTPException(status_code=403, detail="No autorizado.")

        pago.estatus_pago = models.EstatusPago(request.estatus_pago)
        if request.estatus_pago == "completado": pago.fecha_pago = datetime.utcnow()

        db.commit()
        db.refresh(pago)
        return pago