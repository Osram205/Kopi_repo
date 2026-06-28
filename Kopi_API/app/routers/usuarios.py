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
        "matricula": usuario_actual.matricula,
        "correo_institucional": usuario_actual.correo_institucional,
        "estatus_verificacion": usuario_actual.estatus_verificacion,
        "es_conductor": usuario_actual.es_conductor
    }

# 2. ENDPOINT PARA LEVANTAR LA MANO COMO CONDUCTOR
@router.put("/solicitar-conductor")
def solicitar_ser_conductor(
    foto_credencial: UploadFile = File(...),
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
    
    # Mapeamos los archivos entrantes para procesarlos y renombrarlos sistemáticamente
    documentos = [
        (foto_credencial, "foto_credencial"),
        (foto_licencia, "foto_licencia"),
        (tarjeta_circulacion, "tarjeta_circulacion")
    ]
    
    for archivo, columna_db in documentos:
        # Extraemos la extensión original (ej. .jpg, .png)
        ext = os.path.splitext(archivo.filename)[1]
        # Renombramos usando la matrícula única para evitar duplicados o colisiones de nombres
        nombre_limpio = f"{usuario_actual.matricula}_{columna_db}{ext}"
        ruta_final = os.path.join(UPLOAD_DIR, nombre_limpio)
        
        # Guardamos el flujo binario en el disco duro
        with open(ruta_final, "wb") as buffer:
            shutil.copyfileobj(archivo.file, buffer)
            
        # Asignamos el string del nombre a la propiedad del modelo del usuario
        setattr(usuario_actual, columna_db, nombre_limpio)
        
    # Cambiamos el estado de verificación al nuevo flujo controlado
    usuario_actual.estatus_verificacion = "solicitado"
    db.commit()
    
    return {"mensaje": "Expediente digital guardado. Solicitud en proceso de evaluación institucional."}