from fastapi import HTTPException
from sqlalchemy.orm import Session
from app.data import models

class AdminService:
    @staticmethod
    def listar_usuarios_pendientes(db: Session):
        """Lista a todos los alumnos que han solicitado ser conductores (han subido sus 4 documentos)."""
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
        
        # 🔥 FIX 2: Si el admin aprueba los documentos, le damos oficialmente el rol de conductor
        if accion == 'aprobado':
            alumno.es_conductor = True
        elif accion == 'rechazado':
            alumno.es_conductor = False

        db.commit()
        db.refresh(alumno)
        
        return {"mensaje": f"El estatus del alumno {alumno.matricula} ha cambiado a {accion}."}
    
    @staticmethod
    def obtener_metricas(db: Session):
        """Calcula los KPIs en tiempo real del sistema."""
        from sqlalchemy import func
        from datetime import datetime, timedelta

        usuarios_totales = db.query(models.Usuario).count()
        viajes_activos = db.query(models.Viaje).filter(models.Viaje.estatus == models.EstatusViaje.programado).count()
        # Contar cuántos asientos están ocupados en viajes programados
        reservas_aceptadas = db.query(models.Reservacion).filter(
            models.Reservacion.estatus_reserva == models.EstatusReserva.aceptado
        ).count()
        
        # 1. Ahorro CO2 (Aprox. 2.5 kg por reserva compartida completada)
        reservas_completadas = db.query(models.Reservacion).join(models.Viaje).filter(
            models.Viaje.estatus == models.EstatusViaje.completado,
            models.Reservacion.estatus_reserva == models.EstatusReserva.aceptado
        ).count()
        ahorro_co2 = reservas_completadas * 2.5  # kg de CO2
        
        # 2. Demanda por día de la semana (Lunes a Viernes)
        # Asumiendo MySQL/SQLite DATE() extract, pero lo hacemos en memoria por compatibilidad rápida
        # Obtener todos los viajes completados de los últimos 7 días
        hace_7_dias = datetime.utcnow() - timedelta(days=7)
        viajes_recientes = db.query(models.Viaje).filter(
            models.Viaje.created_at >= hace_7_dias
        ).all()
        
        viajes_por_dia = [0, 0, 0, 0, 0, 0, 0] # Lunes=0, Domingo=6
        for v in viajes_recientes:
            dia = v.created_at.weekday()
            viajes_por_dia[dia] += 1
        
        # Filtrar solo de Lunes a Viernes
        demanda_semanal = viajes_por_dia[0:5]
        
        return {
            "usuarios_totales": usuarios_totales,
            "viajes_activos": viajes_activos,
            "alumnos_transportandose": reservas_aceptadas,
            "ahorro_co2": ahorro_co2,
            "demanda_semanal": demanda_semanal
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