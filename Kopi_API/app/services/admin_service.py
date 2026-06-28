from fastapi import HTTPException
from sqlalchemy.orm import Session
from app.data import models

class AdminService:
    @staticmethod
    def listar_usuarios_pendientes(db: Session):
        """Lista a todos los alumnos que han subido credencial pero no han sido aprobados."""
        return db.query(models.Usuario).filter(
            models.Usuario.estatus_verificacion == 'solicitado',
            models.Usuario.deleted_at.is_(None)
        ).all()
    
    @staticmethod
    def listar_directorio_conductores(db: Session, estatus: str = None):
        """Lista a los alumnos evaluados con opción de filtrar por estatus."""
        query = db.query(models.Usuario).filter(models.Usuario.deleted_at.is_(None))
        
        if estatus in ['aprobado', 'rechazado']:
            query = query.filter(models.Usuario.estatus_verificacion == estatus)
        else:
            # Si no viene filtro, muestra ambos
            query = query.filter(models.Usuario.estatus_verificacion.in_(['aprobado', 'rechazado']))
            
        return query.all()

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
    
    @staticmethod
    def obtener_metricas(db: Session):
        """Calcula los KPIs en tiempo real del sistema."""
        usuarios_totales = db.query(models.Usuario).count()
        viajes_activos = db.query(models.Viaje).filter(models.Viaje.estatus == models.EstatusViaje.programado).count()
        # Contar cuántos asientos están ocupados en viajes programados
        reservas_aceptadas = db.query(models.Reservacion).filter(
            models.Reservacion.estatus_reserva == models.EstatusReserva.aceptado
        ).count()
        
        return {
            "usuarios_totales": usuarios_totales,
            "viajes_activos": viajes_activos,
            "alumnos_transportandose": reservas_aceptadas
        }

    @staticmethod
    def suspender_usuario(db: Session, usuario_id: int):
        """El botón rojo: banea a un usuario del sistema."""
        usuario = db.query(models.Usuario).filter(models.Usuario.id == usuario_id).first()
        if not usuario:
            raise HTTPException(status_code=404, detail="Usuario no encontrado.")
        
        # Usamos el soft delete para suspenderlo sin romper las relaciones de la BD
        from datetime import datetime
        usuario.deleted_at = datetime.utcnow()
        db.commit()
        
        return {"mensaje": f"El usuario {usuario.matricula} ha sido suspendido y expulsado del sistema."}
    
    @staticmethod
    def revocar_privilegios_conduccion(db: Session, usuario_id: int):
        """Le quita los permisos de conductor a un alumno, regresándolo a estatus de pasajero."""
        alumno = db.query(models.Usuario).filter(models.Usuario.id == usuario_id).first()
        if not alumno:
            raise HTTPException(status_code=404, detail="Usuario no encontrado.")

        # Modificamos los campos clave para retirarle el rol de conductor
        alumno.estatus_verificacion = 'rechazado'
        alumno.es_conductor = False
        
        # Opcional: Cancelar sus viajes programados activos para que no queden rutas fantasma
        db.query(models.Viaje).filter(
            models.Viaje.conductor_id == usuario_id,
            models.Viaje.estatus == models.EstatusViaje.programado
        ).update({models.Viaje.estatus: models.EstatusViaje.cancelado}, synchronize_session=False)

        db.commit()
        db.refresh(alumno)
        
        return {"mensaje": f"Permisos de conducción revocados con éxito para el alumno {alumno.matricula}."}