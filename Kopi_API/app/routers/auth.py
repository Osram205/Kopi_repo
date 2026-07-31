from fastapi import APIRouter, Depends, status, Form, UploadFile, File, HTTPException
from sqlalchemy.orm import Session
from fastapi.security import OAuth2PasswordRequestForm
import shutil
import os

from app.data import database, models
from app.models import auth_schema
from app.services.auth_service import AuthService

router = APIRouter(prefix="/auth", tags=["Autenticación"])

@router.post("/registro", response_model=auth_schema.UsuarioRespuesta, status_code=status.HTTP_201_CREATED)
def registrar_usuario(
    nombre: str = Form(...),
    apellidos: str = Form(...),
    carrera: str = Form(...),
    matricula: str = Form(...),
    correo_institucional: str = Form(...),
    telefono: str = Form(...),
    contrasena: str = Form(...),
    db: Session = Depends(database.get_db)
):
    # 3. Construir el DTO
    request_dto = auth_schema.UsuarioRegistro(
        nombre=nombre,
        apellidos=apellidos,
        carrera=carrera,
        matricula=matricula,
        correo_institucional=correo_institucional,
        telefono=telefono,
        contrasena=contrasena
    )
    
    usuario = AuthService.registrar_usuario(db, request_dto)
    
    # El estatus inicial del usuario será pendiente hasta que solicite revisión o validemos su correo
    usuario.estatus_verificacion = "pendiente" 
    db.commit()
    
    return usuario

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