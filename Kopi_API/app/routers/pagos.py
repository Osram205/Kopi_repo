from fastapi import APIRouter, Depends, status
from sqlalchemy.orm import Session
from app.data import database, models
from app.models import pago_schema
from app.security.oauth2 import get_current_user
from app.services.pago_service import PagoService

router = APIRouter(prefix="/pagos", tags=["Pagos"])

@router.post("/checkout", status_code=status.HTTP_200_OK)
def iniciar_checkout(request: pago_schema.PagoCrear, db: Session = Depends(database.get_db), usuario: models.Usuario = Depends(get_current_user)):
    return PagoService.crear_sesion_checkout(db, request.reservacion_id, request.metodo_pago, usuario)

@router.post("/confirmar", response_model=pago_schema.PagoRespuesta, status_code=status.HTTP_201_CREATED)
def confirmar_pago(request: pago_schema.PagoConfirmar, db: Session = Depends(database.get_db), usuario: models.Usuario = Depends(get_current_user)):
    return PagoService.confirmar_pago_stripe(db, request.reservacion_id, request.metodo_pago, request.session_id, usuario)
