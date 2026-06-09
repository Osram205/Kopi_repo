from fastapi import APIRouter, Depends, status
from sqlalchemy.orm import Session
from app.data import database, models
from app.models import pago_schema
from app.security.oauth2 import get_current_user
from app.services.pago_service import PagoService

router = APIRouter(prefix="/pagos", tags=["Pagos"])

@router.post("/", response_model=pago_schema.PagoRespuesta, status_code=status.HTTP_201_CREATED)
def crear_pago(request: pago_schema.PagoCrear, db: Session = Depends(database.get_db), usuario: models.Usuario = Depends(get_current_user)):
    return PagoService.crear(db, request, usuario)

@router.put("/{pago_id}/estatus", response_model=pago_schema.PagoRespuesta)
def actualizar_estatus_pago(pago_id: int, request: pago_schema.PagoEstatus, db: Session = Depends(database.get_db), usuario: models.Usuario = Depends(get_current_user)):
    return PagoService.actualizar_estatus(db, pago_id, request, usuario)