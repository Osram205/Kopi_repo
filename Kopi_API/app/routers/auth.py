# app/routers/auth.py
from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from ..data import database, models
from ..models import auth_schema
from ..security.hashing import Hash
from ..security.oauth2 import create_access_token

# Creamos el "router" (agrupador de rutas)
router = APIRouter(
    prefix="/auth",
    tags=["Autenticación"]
)

@router.post("/registro", response_model=auth_schema.UsuarioRespuesta, status_code=status.HTTP_201_CREATED)
def registrar_usuario(request: auth_schema.UsuarioRegistro, db: Session = Depends(database.get_db)):
    
    # 1. Verificar si el correo ya existe en la base de datos
    usuario_existente = db.query(models.Usuario).filter(models.Usuario.correo_institucional == request.correo_institucional).first()
    
    if usuario_existente:
        raise HTTPException(status_code=400, detail="Este correo institucional ya está registrado en Kopi.")

    matricula_existente = db.query(models.Usuario).filter(models.Usuario.matricula == request.matricula).first()
    if matricula_existente:
        raise HTTPException(status_code=400, detail="Esta matrícula ya está registrada en Kopi.")

    # 2. Crear el nuevo usuario mapeando los datos y encriptando la contraseña
    nuevo_usuario = models.Usuario(
        nombre=request.nombre,
        matricula=request.matricula,
        correo_institucional=request.correo_institucional,
        contrasena=Hash.bcrypt(request.contrasena), # ¡Contraseña protegida!
        telefono=request.telefono
    )
    
    # 3. Guardar en MySQL
    db.add(nuevo_usuario)
    db.commit()
    db.refresh(nuevo_usuario) # Obtenemos el ID generado
    
    # 4. FastAPI y Pydantic se encargan de devolver solo lo permitido en UsuarioRespuesta
    return nuevo_usuario


@router.post("/login", response_model=auth_schema.Token)
def login(request: auth_schema.UsuarioLogin, db: Session = Depends(database.get_db)):
    usuario = db.query(models.Usuario).filter(
        models.Usuario.correo_institucional == request.correo_institucional,
        models.Usuario.deleted_at.is_(None),
    ).first()

    if not usuario or not Hash.verify(usuario.contrasena, request.contrasena):
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="Credenciales inválidas.")

    return {
        "access_token": create_access_token(subject=str(usuario.id)),
        "token_type": "bearer",
    }
