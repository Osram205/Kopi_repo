from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from app.data import database, models
from app.security.oauth2 import get_current_admin
from app.services.admin_service import AdminService

# Todas las rutas aquí requerirán ser administrador
router = APIRouter(
    prefix="/admin", 
    tags=["Administración"],
    dependencies=[Depends(get_current_admin)]
)

@router.get("/verificaciones/pendientes")
def listar_verificaciones_pendientes(db: Session = Depends(database.get_db)):
    return AdminService.listar_usuarios_pendientes(db)

@router.put("/usuarios/{usuario_id}/verificacion")
def evaluar_alumno(usuario_id: int, accion: str, db: Session = Depends(database.get_db)):
    # accion debe ser ?accion=aprobado o ?accion=rechazado en la URL
    return AdminService.evaluar_verificacion(db, usuario_id, accion)

@router.get("/metricas")
def obtener_kpis(db: Session = Depends(database.get_db)):
    return AdminService.obtener_metricas(db)

@router.delete("/usuarios/{usuario_id}/suspender")
def suspender_alumno(usuario_id: int, db: Session = Depends(database.get_db)):
    return AdminService.suspender_usuario(db, usuario_id)

@router.get("/usuarios/directorio")
def obtener_directorio(estatus: str = None, db: Session = Depends(database.get_db)):
    return AdminService.listar_directorio_conductores(db, estatus)

@router.put("/usuarios/{usuario_id}/revocar")
def revocar_conduccion(usuario_id: int, db: Session = Depends(database.get_db)):
    return AdminService.revocar_privilegios_conduccion(db, usuario_id)