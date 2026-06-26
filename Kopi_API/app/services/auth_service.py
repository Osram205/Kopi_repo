from sqlalchemy.orm import Session
from fastapi import HTTPException, status

from app.data import models
from app.models import auth_schema
from app.security.hashing import Hash
from app.security.oauth2 import create_access_token

class AuthService:
    @staticmethod
    def registrar_usuario(db: Session, request: auth_schema.UsuarioRegistro):
        if not request.correo_institucional.endswith('@upq.edu.mx'):
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST, 
                detail="Acceso denegado. Solo se permiten registros con el correo institucional @upq.edu.mx"
            )

        # 2. Validaciones de duplicados
        if db.query(models.Usuario).filter(models.Usuario.correo_institucional == request.correo_institucional).first():
            raise HTTPException(status_code=400, detail="Este correo institucional ya está registrado en Kopi.")

        if db.query(models.Usuario).filter(models.Usuario.matricula == request.matricula).first():
            raise HTTPException(status_code=400, detail="Esta matrícula ya está registrada en Kopi.")

        # 3. Creación del usuario con estatus 'pendiente' por defecto
        nuevo_usuario = models.Usuario(
            nombre=request.nombre,
            matricula=request.matricula,
            correo_institucional=request.correo_institucional,
            contrasena=Hash.bcrypt(request.contrasena),
            telefono=request.telefono,
            estatus_verificacion='pendiente', # <- Permite acciones de pasajero, bloquea conductor
            es_conductor=False,
            es_admin=False
        )
        
        db.add(nuevo_usuario)
        db.commit()
        db.refresh(nuevo_usuario)
        
        return nuevo_usuario

    @staticmethod
    def login(db: Session, request: auth_schema.UsuarioLogin):
        usuario = db.query(models.Usuario).filter(
            models.Usuario.correo_institucional == request.correo_institucional,
            models.Usuario.deleted_at.is_(None),
        ).first()

        # ORDEN CORRECTO: (texto plano de la petición, hash de la base de datos)
        if not usuario or not Hash.verify(request.contrasena, usuario.contrasena):
            raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="Credenciales inválidas.")

        return {
            "access_token": create_access_token(subject=str(usuario.id)),
            "token_type": "bearer",
        }