from sqlalchemy.orm import Session, joinedload
from fastapi import HTTPException
from datetime import datetime
from sqlalchemy import func
import struct

from app.data import models
from app.models import viaje_schema

class ViajeService:
    @staticmethod
    def listar(db: Session, origen: str | None, destino: str | None, fecha_salida: str | None):
        query = db.query(models.Viaje).options(joinedload(models.Viaje.paradas)).filter(
            models.Viaje.estatus == models.EstatusViaje.programado,
            models.Viaje.asientos_disponibles > 0,
            models.Viaje.deleted_at.is_(None),
        )

        if origen:
            query = query.filter(models.Viaje.origen.ilike(f"%{origen}%"))
        if destino:
            query = query.filter(models.Viaje.destino.ilike(f"%{destino}%"))
        if fecha_salida:
            query = query.filter(models.Viaje.fecha_salida == fecha_salida)

        return query.order_by(models.Viaje.fecha_salida, models.Viaje.hora_salida).all()
    @staticmethod
    def obtener(db: Session, viaje_id: int):
        viaje = db.query(models.Viaje).filter(
            models.Viaje.id == viaje_id,
            models.Viaje.deleted_at.is_(None)
        ).first()

        if not viaje:
            raise HTTPException(status_code=404, detail="Viaje no encontrado.")

        # Ya no hay traductor manual aquí. Pydantic lo hará automático.
        return viaje
    @staticmethod
    def crear(db: Session, request: viaje_schema.ViajeCrear, usuario: models.Usuario):
        if getattr(usuario, 'estatus_verificacion', 'pendiente') != 'aprobado':
            raise HTTPException(
                status_code=403, 
                detail="Tu cuenta aún no está verificada. No puedes publicar viajes hasta que se valide tu credencial institucional."
            )
        
        # 1. Validaciones
        vehiculo = db.query(models.Vehiculo).filter(
            models.Vehiculo.id == request.vehiculo_id,
            models.Vehiculo.conductor_id == usuario.id,
            models.Vehiculo.deleted_at.is_(None),
        ).first()

        if not vehiculo:
            raise HTTPException(status_code=403, detail="El vehículo no pertenece al conductor autenticado.")
        if request.asientos_disponibles > vehiculo.asientos_totales:
            raise HTTPException(status_code=422, detail="Los asientos disponibles exceden los asientos del vehículo.")

        # 2. Inserción de Viaje
        data = request.model_dump(exclude={"paradas"})
        viaje = models.Viaje(**data, conductor_id=usuario.id)
        db.add(viaje)
        db.flush() # flush() genera el viaje.id temporal necesario para las paradas

        # 3. Inserción de Paradas (Corregido para GEOMETRY / POINT)
        for parada in request.paradas:
            # Separar "latitud,longitud"
            partes = parada.coordenadas.split(',')
            lat = partes[0].strip()
            lon = partes[1].strip()
            
            # Formatear para MySQL: POINT(longitud latitud)
            punto_espacial = func.ST_GeomFromText(f"POINT({lon} {lat})")
            
            # Volcar datos excluyendo el string original e inyectando la variable espacial
            parada_data = parada.model_dump(exclude={"coordenadas"})
            nueva_parada = models.ParadaViaje(
                **parada_data, 
                coordenadas=punto_espacial, 
                viaje_id=viaje.id
            )
            db.add(nueva_parada)
            
        usuario.es_conductor = True
        db.commit()
        db.refresh(viaje)
        
        return ViajeService.obtener(db, viaje.id)

    @staticmethod
    def actualizar(db: Session, viaje_id: int, request: viaje_schema.ViajeActualizar, usuario: models.Usuario):
        viaje = db.query(models.Viaje).filter(
            models.Viaje.id == viaje_id,
            models.Viaje.conductor_id == usuario.id,
            models.Viaje.deleted_at.is_(None),
        ).first()

        if not viaje:
            raise HTTPException(status_code=404, detail="Viaje no encontrado.")

        for field, value in request.model_dump(exclude_unset=True).items():
            setattr(viaje, field, value)

        db.commit()
        db.refresh(viaje)
        return ViajeService.obtener(db, viaje.id)

    @staticmethod
    def cancelar(db: Session, viaje_id: int, usuario: models.Usuario):
        viaje = db.query(models.Viaje).filter(
            models.Viaje.id == viaje_id,
            models.Viaje.conductor_id == usuario.id,
            models.Viaje.deleted_at.is_(None),
        ).first()

        if not viaje:
            raise HTTPException(status_code=404, detail="Viaje no encontrado.")

        viaje.estatus = models.EstatusViaje.cancelado
        viaje.deleted_at = datetime.utcnow()
        db.commit()
        
        return {"mensaje": "Viaje cancelado correctamente."}