from fastapi import APIRouter, Depends, HTTPException, Query, status
from sqlalchemy.orm import Session, joinedload

from app.data import database, models
from app.models import viaje_schema
from app.security.oauth2 import get_current_user

router = APIRouter(prefix="/viajes", tags=["Viajes"])


@router.get("/", response_model=list[viaje_schema.ViajeRespuesta])
def listar_viajes(
    origen: str | None = Query(default=None),
    destino: str | None = Query(default=None),
    fecha_salida: str | None = Query(default=None),
    db: Session = Depends(database.get_db),
):
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


@router.get("/{viaje_id}", response_model=viaje_schema.ViajeRespuesta)
def obtener_viaje(viaje_id: int, db: Session = Depends(database.get_db)):
    viaje = db.query(models.Viaje).options(joinedload(models.Viaje.paradas)).filter(
        models.Viaje.id == viaje_id,
        models.Viaje.deleted_at.is_(None),
    ).first()

    if not viaje:
        raise HTTPException(status_code=404, detail="Viaje no encontrado.")

    return viaje


@router.post("/", response_model=viaje_schema.ViajeRespuesta, status_code=status.HTTP_201_CREATED)
def crear_viaje(
    request: viaje_schema.ViajeCrear,
    db: Session = Depends(database.get_db),
    usuario: models.Usuario = Depends(get_current_user),
):
    vehiculo = db.query(models.Vehiculo).filter(
        models.Vehiculo.id == request.vehiculo_id,
        models.Vehiculo.conductor_id == usuario.id,
        models.Vehiculo.deleted_at.is_(None),
    ).first()

    if not vehiculo:
        raise HTTPException(status_code=403, detail="El vehículo no pertenece al conductor autenticado.")
    if request.asientos_disponibles > vehiculo.asientos_totales:
        raise HTTPException(status_code=422, detail="Los asientos disponibles exceden los asientos del vehículo.")

    data = request.model_dump(exclude={"paradas"})
    viaje = models.Viaje(**data, conductor_id=usuario.id)
    db.add(viaje)
    db.flush()

    for parada in request.paradas:
        db.add(models.ParadaViaje(**parada.model_dump(), viaje_id=viaje.id))

    usuario.es_conductor = True
    db.commit()
    db.refresh(viaje)
    return obtener_viaje(viaje.id, db)


@router.put("/{viaje_id}", response_model=viaje_schema.ViajeRespuesta)
def actualizar_viaje(
    viaje_id: int,
    request: viaje_schema.ViajeActualizar,
    db: Session = Depends(database.get_db),
    usuario: models.Usuario = Depends(get_current_user),
):
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
    return obtener_viaje(viaje.id, db)


@router.delete("/{viaje_id}")
def cancelar_viaje(
    viaje_id: int,
    db: Session = Depends(database.get_db),
    usuario: models.Usuario = Depends(get_current_user),
):
    viaje = db.query(models.Viaje).filter(
        models.Viaje.id == viaje_id,
        models.Viaje.conductor_id == usuario.id,
        models.Viaje.deleted_at.is_(None),
    ).first()

    if not viaje:
        raise HTTPException(status_code=404, detail="Viaje no encontrado.")

    from datetime import datetime

    viaje.estatus = models.EstatusViaje.cancelado
    viaje.deleted_at = datetime.utcnow()
    db.commit()
    return {"mensaje": "Viaje cancelado correctamente."}
