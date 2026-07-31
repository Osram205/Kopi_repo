from fastapi import APIRouter, Depends, status, Form, UploadFile, File, HTTPException
from sqlalchemy.orm import Session
from fastapi.security import OAuth2PasswordRequestForm
import shutil
import os

from app.data import database, models
from app.models import auth_schema
from app.services.auth_service import AuthService
from app.services.ocr_service import OCRService

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
    foto_credencial_frente: UploadFile = File(...),
    foto_credencial_trasera: UploadFile = File(...),
    db: Session = Depends(database.get_db)
):
    # 1. Ejecutar OCR para validar credencial institucional
    imagen_frente_bytes = foto_credencial_frente.file.read()
    imagen_trasera_bytes = foto_credencial_trasera.file.read()
    es_valida = OCRService.validar_credencial(imagen_frente_bytes, imagen_trasera_bytes, matricula, nombre)
    
    if not es_valida:
        raise HTTPException(status_code=400, detail="La credencial no pudo ser validada. Asegúrate de que sea una credencial de la UPQ y la foto sea legible.")
    
    # 2. Guardar las imágenes físicamente
    UPLOAD_DIR = "static/uploads"
    os.makedirs(UPLOAD_DIR, exist_ok=True)
    
    ext_frente = os.path.splitext(foto_credencial_frente.filename)[1]
    nombre_limpio_frente = f"{matricula}_credencial_frente_registro{ext_frente}"
    ruta_final_frente = os.path.join(UPLOAD_DIR, nombre_limpio_frente)
    
    with open(ruta_final_frente, "wb") as buffer:
        buffer.write(imagen_frente_bytes)

    ext_trasera = os.path.splitext(foto_credencial_trasera.filename)[1]
    nombre_limpio_trasera = f"{matricula}_credencial_trasera_registro{ext_trasera}"
    ruta_final_trasera = os.path.join(UPLOAD_DIR, nombre_limpio_trasera)
    
    with open(ruta_final_trasera, "wb") as buffer:
        buffer.write(imagen_trasera_bytes)

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
    
    # Asignar credencial y aprobar verificación básica
    usuario.foto_credencial_frente = nombre_limpio_frente
    usuario.foto_credencial_trasera = nombre_limpio_trasera
    # Como la credencial fue aprobada por OCR, lo marcamos como aprobado en estatus base
    usuario.estatus_verificacion = "aprobado" 
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