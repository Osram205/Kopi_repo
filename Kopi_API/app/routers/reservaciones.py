from fastapi import APIRouter, Depends, status
from sqlalchemy.orm import Session
from app.data import database, models
from app.models import reserva_schema
from app.security.oauth2 import get_current_user
from app.services.reservacion_service import ReservacionService

router = APIRouter(prefix="/reservaciones", tags=["Reservaciones"])

@router.get("/", response_model=list[reserva_schema.ReservacionRespuesta])
def listar_reservaciones(db: Session = Depends(database.get_db), usuario: models.Usuario = Depends(get_current_user)):
    return ReservacionService.listar(db, usuario)

@router.post("/", response_model=reserva_schema.ReservacionRespuesta, status_code=status.HTTP_201_CREATED)
def crear_reservacion(request: reserva_schema.ReservacionCrear, db: Session = Depends(database.get_db), usuario: models.Usuario = Depends(get_current_user)):
    return ReservacionService.crear(db, request, usuario)

@router.put("/{reservacion_id}/estatus", response_model=reserva_schema.ReservacionRespuesta)
def actualizar_estatus(reservacion_id: int, request: reserva_schema.ReservacionEstatus, db: Session = Depends(database.get_db), usuario: models.Usuario = Depends(get_current_user)):
    return ReservacionService.actualizar_estatus(db, reservacion_id, request, usuario)