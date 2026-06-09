from fastapi import HTTPException
from sqlalchemy.orm import Session
from app.data import models

class AdminService:
    @staticmethod
    def listar_usuarios_pendientes(db: Session):
        """Lista a todos los alumnos que han subido credencial pero no han sido aprobados."""
        return db.query(models.Usuario).filter(
            models.Usuario.estatus_verificacion == 'pendiente',
            models.Usuario.deleted_at.is_(None)
        ).all()

    @staticmethod
    def evaluar_verificacion(db: Session, usuario_id: int, accion: str):
        """Aprueba o rechaza la credencial de un alumno."""
        if accion not in ['aprobado', 'rechazado']:
            raise HTTPException(status_code=422, detail="Acción inválida. Usa 'aprobado' o 'rechazado'.")

        alumno = db.query(models.Usuario).filter(models.Usuario.id == usuario_id).first()
        if not alumno:
            raise HTTPException(status_code=404, detail="Usuario no encontrado.")

        alumno.estatus_verificacion = accion
        db.commit()
        db.refresh(alumno)
        
        return {"mensaje": f"El estatus del alumno {alumno.matricula} ha cambiado a {accion}."}