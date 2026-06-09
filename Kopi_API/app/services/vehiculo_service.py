from datetime import datetime
from fastapi import HTTPException
from sqlalchemy.orm import Session
from app.data import models
from app.models import vehiculos_schema

class VehiculoService:
    @staticmethod
    def listar(db: Session, usuario: models.Usuario):
        return db.query(models.Vehiculo).filter(
            models.Vehiculo.conductor_id == usuario.id,
            models.Vehiculo.deleted_at.is_(None),
        ).all()

    @staticmethod
    def crear(db: Session, request: vehiculos_schema.VehiculoCrear, usuario: models.Usuario):
        if db.query(models.Vehiculo).filter(models.Vehiculo.placas == request.placas).first():
            raise HTTPException(status_code=400, detail="Las placas ya están registradas.")

        vehiculo = models.Vehiculo(**request.model_dump(), conductor_id=usuario.id)
        usuario.es_conductor = True

        db.add(vehiculo)
        db.commit()
        db.refresh(vehiculo)
        return vehiculo

    @staticmethod
    def actualizar(db: Session, vehiculo_id: int, request: vehiculos_schema.VehiculoCrear, usuario: models.Usuario):
        vehiculo = db.query(models.Vehiculo).filter(
            models.Vehiculo.id == vehiculo_id, models.Vehiculo.conductor_id == usuario.id, models.Vehiculo.deleted_at.is_(None)
        ).first()

        if not vehiculo: raise HTTPException(status_code=404, detail="Vehículo no encontrado.")

        placas_existentes = db.query(models.Vehiculo).filter(models.Vehiculo.placas == request.placas, models.Vehiculo.id != vehiculo.id).first()
        if placas_existentes: raise HTTPException(status_code=400, detail="Las placas ya están registradas.")

        for field, value in request.model_dump().items(): setattr(vehiculo, field, value)
        db.commit()
        db.refresh(vehiculo)
        return vehiculo

    @staticmethod
    def eliminar(db: Session, vehiculo_id: int, usuario: models.Usuario):
        vehiculo = db.query(models.Vehiculo).filter(
            models.Vehiculo.id == vehiculo_id, models.Vehiculo.conductor_id == usuario.id, models.Vehiculo.deleted_at.is_(None)
        ).first()

        if not vehiculo: raise HTTPException(status_code=404, detail="Vehículo no encontrado.")

        vehiculo.deleted_at = datetime.utcnow()
        db.commit()
        return {"mensaje": "Vehículo eliminado correctamente."}
    