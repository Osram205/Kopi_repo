from fastapi import APIRouter, Depends, status
from sqlalchemy.orm import Session
from app.data import database, models
from app.models import calificacion_schema
from app.security.oauth2 import get_current_user
from app.services.calificacion_service import CalificacionService

router = APIRouter(prefix="/calificaciones", tags=["Calificaciones"])

@router.post("/", response_model=calificacion_schema.CalificacionRespuesta, status_code=status.HTTP_201_CREATED)
def crear_calificacion(request: calificacion_schema.CalificacionCrear, db: Session = Depends(database.get_db), usuario: models.Usuario = Depends(get_current_user)):
    return CalificacionService.crear(db, request, usuario)