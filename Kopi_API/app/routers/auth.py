from fastapi import APIRouter, Depends, status
from sqlalchemy.orm import Session
from fastapi.security import OAuth2PasswordRequestForm

from app.data import database
from app.models import auth_schema
from app.services.auth_service import AuthService

router = APIRouter(prefix="/auth", tags=["Autenticación"])

@router.post("/registro", response_model=auth_schema.UsuarioRespuesta, status_code=status.HTTP_201_CREATED)
def registrar_usuario(request: auth_schema.UsuarioRegistro, db: Session = Depends(database.get_db)):
    return AuthService.registrar_usuario(db, request)

@router.post("/login")
def login(request: OAuth2PasswordRequestForm = Depends(), db: Session = Depends(database.get_db)):
    login_data = auth_schema.UsuarioLogin(
        correo_institucional=request.username, 
        contrasena=request.password
    )
    return AuthService.login(db, login_data)

@router.post("/verificar-identidad")
def verificar_identidad(request: auth_schema.VerificarIdentidad, db: Session = Depends(database.get_db)):
    return AuthService.verificar_identidad_recuperacion(db, request)

@router.post("/restablecer-con-token")
def restablecer_con_token(request: auth_schema.RestablecerConToken, db: Session = Depends(database.get_db)):
    return AuthService.restablecer_password_con_token(db, request)