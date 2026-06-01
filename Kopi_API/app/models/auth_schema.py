from pydantic import BaseModel, EmailStr, validator
from typing import Optional
from datetime import datetime

class UsuarioRegistro(BaseModel):
    nombre: str
    matricula: str
    correo_institucional: EmailStr
    contrasena: str
    telefono: str
    
    
    @validator('correo_institucional')
    def validar_dominio_upq(cls, v):
        if not v.endswith('@upq.edu.mx'):
            raise ValueError('El correo debe pertenecer al dominio institucional (@upq.edu.mx)')
        return v

class UsuarioLogin(BaseModel):
    correo_institucional: EmailStr
    contrasena: str

class UsuarioRespuesta(BaseModel):
    id: int
    nombre: str
    matricula: str
    correo_institucional: str
    telefono: str
    foto_credencial: Optional[str] = None
    estatus_verificacion: str
    es_conductor: bool
    created_at: datetime

    class Config:
        from_attributes = True 

class Token(BaseModel):
    access_token: str
    token_type: str
