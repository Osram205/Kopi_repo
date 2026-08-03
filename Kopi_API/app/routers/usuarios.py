import os
import shutil
from fastapi import APIRouter, Depends, HTTPException, status,File, UploadFile
from sqlalchemy.orm import Session

from app.data import database, models
from app.security import oauth2

router = APIRouter(prefix="/usuarios", tags=["Usuarios"])

@router.get("/perfil")
def obtener_perfil(usuario_actual: models.Usuario = Depends(oauth2.get_current_user)):
    return {
        "id": usuario_actual.id,
        "nombre": usuario_actual.nombre,
        "apellidos": usuario_actual.apellidos,
        "carrera": usuario_actual.carrera,
        "matricula": usuario_actual.matricula,
        "foto_perfil": usuario_actual.foto_perfil,
        "correo_institucional": usuario_actual.correo_institucional,
        "telefono": usuario_actual.telefono,
        "estatus_verificacion": usuario_actual.estatus_verificacion,
        "es_conductor": usuario_actual.es_conductor
    }

from fastapi import Form, UploadFile, File
import shutil
import os

@router.post("/perfil")
def actualizar_perfil(
    telefono: str = Form(None),
    foto_perfil: UploadFile = File(None),
    db: Session = Depends(database.get_db),
    usuario_actual: models.Usuario = Depends(oauth2.get_current_user)
):
    if telefono:
        usuario_actual.telefono = telefono
        
    if foto_perfil:
        UPLOAD_DIR = "static/uploads"
        os.makedirs(UPLOAD_DIR, exist_ok=True)
        ext = os.path.splitext(foto_perfil.filename)[1]
        nombre_limpio = f"{usuario_actual.matricula}_perfil{ext}"
        ruta_final = os.path.join(UPLOAD_DIR, nombre_limpio)
        
        with open(ruta_final, "wb") as buffer:
            shutil.copyfileobj(foto_perfil.file, buffer)
            
        usuario_actual.foto_perfil = nombre_limpio
        
    db.commit()
    return {"mensaje": "Perfil actualizado correctamente", "foto_perfil": usuario_actual.foto_perfil}

# 2. ENDPOINT PARA LEVANTAR LA MANO COMO CONDUCTOR
@router.post("/solicitar-conductor")
def solicitar_ser_conductor(
    foto_credencial_frente: UploadFile = File(...),
    poliza_seguro: UploadFile = File(...),
    foto_licencia: UploadFile = File(...),
    tarjeta_circulacion: UploadFile = File(...),
    db: Session = Depends(database.get_db), 
    usuario_actual: models.Usuario = Depends(oauth2.get_current_user)
):
    if usuario_actual.estatus_verificacion == "aprobado":
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST, 
            detail="Ya eres un conductor verificado en el sistema."
        )
        
    # Definimos la ruta estática para guardar los archivos físicos
    UPLOAD_DIR = "static/uploads"
    os.makedirs(UPLOAD_DIR, exist_ok=True)
    
    documentos = [
        (foto_credencial_frente, "foto_credencial_frente"),
        (poliza_seguro, "poliza_seguro"),
        (foto_licencia, "foto_licencia"),
        (tarjeta_circulacion, "tarjeta_circulacion")
    ]
    
    for archivo, columna_db in documentos:
        # Extraemos la extensión original (ej. .jpg, .png)
        ext = os.path.splitext(archivo.filename)[1]
        nombre_limpio = f"{usuario_actual.matricula}_{columna_db}{ext}"
        ruta_final = os.path.join(UPLOAD_DIR, nombre_limpio)
        
        # Restaurando el método original sin lectura en memoria (shutil.copyfileobj)
        with open(ruta_final, "wb") as buffer:
            shutil.copyfileobj(archivo.file, buffer)
            
        # Asignamos el string del nombre a la propiedad del modelo del usuario
        setattr(usuario_actual, columna_db, nombre_limpio)
        
    # Cambiamos el estado de verificación al nuevo flujo controlado
    usuario_actual.estatus_verificacion = "solicitado"
    db.commit()
    
    return {"mensaje": "Expediente digital guardado. Solicitud en proceso de evaluación institucional."}

from pydantic import BaseModel

class TokenRequest(BaseModel):
    fcm_token: str

@router.post("/fcm-token")
def guardar_fcm_token(
    request: TokenRequest,
    db: Session = Depends(database.get_db),
    usuario_actual: models.Usuario = Depends(oauth2.get_current_user)
):
    usuario_actual.fcm_token = request.fcm_token
    db.commit()
    return {"mensaje": "Token Push guardado exitosamente"}