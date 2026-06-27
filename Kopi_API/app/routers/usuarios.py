from fastapi import APIRouter, Depends, HTTPException, status
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
    db: Session = Depends(database.get_db), 
    usuario_actual: models.Usuario = Depends(oauth2.get_current_user)
):
    if usuario_actual.estatus_verificacion == "aprobado":
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST, 
            detail="Ya eres un conductor verificado en el sistema."
        )
        
    # Cambiamos el estatus en MySQL para que el panel de Flask lo detecte
    usuario_actual.estatus_verificacion = "solicitado"
    db.commit()
    
    return {"mensaje": "Solicitud enviada con éxito al comité administrador de la UPQ."}