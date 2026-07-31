import stripe
from datetime import datetime
from fastapi import HTTPException
from sqlalchemy.orm import Session
from app.data import models
from app.models import pago_schema
from app.core.config import settings

stripe.api_key = settings.STRIPE_SECRET_KEY

class PagoService:
    @staticmethod
    def crear_sesion_checkout(db: Session, reservacion_id: int, metodo_pago: str, usuario: models.Usuario):
        if metodo_pago not in {"tarjeta", "transferencia"}: 
            raise HTTPException(status_code=422, detail="Método de pago inválido.")
        
        reservacion = db.query(models.Reservacion).filter(models.Reservacion.id == reservacion_id).first()
        if not reservacion or reservacion.pasajero_id != usuario.id: 
            raise HTTPException(status_code=404, detail="Reservación no autorizada o inexistente.")
        if reservacion.estatus_reserva != models.EstatusReserva.aceptado: 
            raise HTTPException(status_code=422, detail="La reservación debe estar aceptada.")

        existente = db.query(models.Pago).filter(models.Pago.reservacion_id == reservacion.id).first()
        if existente: 
            raise HTTPException(status_code=422, detail="Esta reservación ya fue pagada.")

        viaje = db.query(models.Viaje).filter(models.Viaje.id == reservacion.viaje_id).first()
        monto_total = float(reservacion.asientos_solicitados * viaje.costo_por_asiento)

        # MOCK PAYMENT IF NO STRIPE KEY
        if not stripe.api_key or str(stripe.api_key).strip() == "" or stripe.api_key == "None":
            mock_url = f'{settings.KOPI_WEB_URL}/mis-viajes?pago_exitoso=true&session_id=mock_session_{reservacion.id}&reservacion_id={reservacion.id}&metodo={metodo_pago}'
            return {"checkout_url": mock_url}

        try:
            session = stripe.checkout.Session.create(
                payment_method_types=['card'],
                line_items=[{
                    'price_data': {
                        'currency': 'mxn',
                        'product_data': {'name': f'Viaje KOPI #{viaje.id} - {viaje.origen} a {viaje.destino}'},
                        'unit_amount': int(monto_total * 100),
                    },
                    'quantity': 1,
                }],
                mode='payment',
                success_url=f'{settings.KOPI_WEB_URL}/mis-viajes?pago_exitoso=true&session_id={{CHECKOUT_SESSION_ID}}&reservacion_id={reservacion.id}&metodo={metodo_pago}',
                cancel_url=f'{settings.KOPI_WEB_URL}/mis-viajes?pago_cancelado=true',
            )
            return {"checkout_url": session.url}
        except Exception as e:
            print(f"Stripe Error: {e}")
            raise HTTPException(status_code=500, detail="Error con la pasarela bancaria.")

    @staticmethod
    def confirmar_pago_stripe(db: Session, reservacion_id: int, metodo_pago: str, session_id: str, usuario: models.Usuario):
        reservacion = db.query(models.Reservacion).filter(models.Reservacion.id == reservacion_id).first()
        if not reservacion: raise HTTPException(status_code=404, detail="Reservacion no encontrada")
        viaje = db.query(models.Viaje).filter(models.Viaje.id == reservacion.viaje_id).first()
        
        monto_real = 0.0

        if not stripe.api_key or str(stripe.api_key).strip() == "" or stripe.api_key == "None":
            if session_id == f"mock_session_{reservacion_id}":
                monto_real = float(reservacion.asientos_solicitados * viaje.costo_por_asiento)
            else:
                raise HTTPException(status_code=400, detail="Fallo de auditoría criptográfica local. Pago rechazado.")
        else:
            try:
                sesion_stripe = stripe.checkout.Session.retrieve(session_id)
                if sesion_stripe.payment_status != "paid":
                    raise HTTPException(status_code=400, detail="La sesión no figura como pagada.")
                monto_real = sesion_stripe.amount_total / 100.0
            except Exception:
                raise HTTPException(status_code=400, detail="Fallo de auditoría criptográfica. Pago rechazado.")

        existente = db.query(models.Pago).filter(models.Pago.reservacion_id == reservacion_id).first()
        if existente: return existente

        pago = models.Pago(
            reservacion_id=reservacion_id,
            monto=monto_real,
            metodo_pago=models.MetodoPago(metodo_pago),
            estatus_pago=models.EstatusPago.completado,
            fecha_pago=datetime.utcnow()
        )
        db.add(pago)
        db.commit()
        db.refresh(pago)
        return pago
