from sqlalchemy import Column, Integer, String, Boolean, ForeignKey, DateTime, Date, Time, Numeric, Enum as SQLEnum, Text
from sqlalchemy.orm import relationship
from datetime import datetime
import enum
from .database import Base  # Importación clave compartida con database.py

# ==========================================
# DEFINICIÓN DE ENUMS (Enumeraciones)
# ==========================================

class EstatusVerificacion(str, enum.Enum):
    pendiente = 'pendiente'
    aprobado = 'aprobado'
    rechazado = 'rechazado'

class EstatusViaje(str, enum.Enum):
    programado = 'programado'
    en_curso = 'en_curso'
    completado = 'completado'
    cancelado = 'cancelado'

class EstatusReserva(str, enum.Enum):
    solicitado = 'solicitado'
    aceptado = 'aceptado'
    rechazado = 'rechazado'
    cancelado = 'cancelado'

class MetodoPago(str, enum.Enum):
    tarjeta = 'tarjeta'
    transferencia = 'transferencia'

class EstatusPago(str, enum.Enum):
    pendiente = 'pendiente'
    completado = 'completado'
    reembolsado = 'reembolsado'

class RolEvaluador(str, enum.Enum):
    conductor = 'conductor'
    pasajero = 'pasajero'


# ==========================================
# MODELOS DE LA BASE DE DATOS
# ==========================================

class Usuario(Base):
    __tablename__ = "usuarios"

    id = Column(Integer, primary_key=True, autoincrement=True)
    nombre = Column(String(100), nullable=False)
    matricula = Column(String(20), unique=True, index=True, nullable=False)
    correo_institucional = Column(String(100), unique=True, index=True, nullable=False)
    contrasena = Column(String(255), nullable=False)
    telefono = Column(String(15), nullable=False)
    foto_credencial = Column(String(255), nullable=True)
    estatus_verificacion = Column(String(20), default="pendiente")
    es_conductor = Column(Boolean, default=False)
    fcm_token = Column(String(512), nullable=True)
    es_admin = Column(Boolean, default=False, nullable=False)
    
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    deleted_at = Column(DateTime, nullable=True)

    # Relaciones
    vehiculos = relationship("Vehiculo", back_populates="conductor")
    viajes_como_conductor = relationship("Viaje", back_populates="conductor")
    reservaciones_como_pasajero = relationship("Reservacion", back_populates="pasajero")
    calificaciones_emitidas = relationship("Calificacion", foreign_keys="[Calificacion.evaluador_id]", back_populates="evaluador")
    calificaciones_recibidas = relationship("Calificacion", foreign_keys="[Calificacion.evaluado_id]", back_populates="evaluado")


class Vehiculo(Base):
    __tablename__ = "vehiculos"

    id = Column(Integer, primary_key=True, autoincrement=True)
    conductor_id = Column(Integer, ForeignKey("usuarios.id"), nullable=False)
    placas = Column(String(10), unique=True, index=True, nullable=False)
    marca = Column(String(50), nullable=False)
    modelo = Column(String(50), nullable=False)
    color = Column(String(30), nullable=False)
    asientos_totales = Column(Integer, nullable=False)
    
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    deleted_at = Column(DateTime, nullable=True)

    # Relaciones
    conductor = relationship("Usuario", back_populates="vehiculos")
    viajes = relationship("Viaje", back_populates="vehiculo")


class Viaje(Base):
    __tablename__ = "viajes"

    id = Column(Integer, primary_key=True, autoincrement=True)
    conductor_id = Column(Integer, ForeignKey("usuarios.id"), nullable=False)
    vehiculo_id = Column(Integer, ForeignKey("vehiculos.id"), nullable=False)
    origen = Column(String(255), nullable=False)
    destino = Column(String(255), nullable=False)
    fecha_salida = Column(Date, nullable=False)
    hora_salida = Column(Time, nullable=False)
    asientos_disponibles = Column(Integer, nullable=False)
    costo_por_asiento = Column(Numeric(10, 2), nullable=False)
    estatus = Column(SQLEnum(EstatusViaje), default=EstatusViaje.programado)
    
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    deleted_at = Column(DateTime, nullable=True)

    # Relaciones
    conductor = relationship("Usuario", back_populates="viajes_como_conductor")
    vehiculo = relationship("Vehiculo", back_populates="viajes")
    paradas = relationship("ParadaViaje", back_populates="viaje", cascade="all, delete-orphan")
    reservaciones = relationship("Reservacion", back_populates="viaje")
    calificaciones = relationship("Calificacion", back_populates="viaje")


class ParadaViaje(Base):
    __tablename__ = "paradas_viaje"

    id = Column(Integer, primary_key=True, autoincrement=True)
    viaje_id = Column(Integer, ForeignKey("viajes.id"), nullable=False)
    nombre_parada = Column(String(150), nullable=False)
    coordenadas = Column(String(100), nullable=False)  # Almacena "latitud,longitud" para consumo ágil de mapas
    orden = Column(Integer, nullable=False)  # Secuencia de paradas (1, 2, 3...)
    
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    # Relaciones
    viaje = relationship("Viaje", back_populates="paradas")
    reservaciones = relationship("Reservacion", back_populates="parada_subida")


class Reservacion(Base):
    __tablename__ = "reservaciones"

    id = Column(Integer, primary_key=True, autoincrement=True)
    viaje_id = Column(Integer, ForeignKey("viajes.id"), nullable=False)
    pasajero_id = Column(Integer, ForeignKey("usuarios.id"), nullable=False)
    parada_subida_id = Column(Integer, ForeignKey("paradas_viaje.id"), nullable=False)
    asientos_solicitados = Column(Integer, default=1, nullable=False)
    estatus_reserva = Column(SQLEnum(EstatusReserva), default=EstatusReserva.solicitado)
    
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    # Relaciones
    viaje = relationship("Viaje", back_populates="reservaciones")
    pasajero = relationship("Usuario", back_populates="reservaciones_como_pasajero")
    parada_subida = relationship("ParadaViaje", back_populates="reservaciones")
    pago = relationship("Pago", uselist=False, back_populates="reservacion")  # uselist=False define relación 1 a 1


class Pago(Base):
    __tablename__ = "pagos"

    id = Column(Integer, primary_key=True, autoincrement=True)
    reservacion_id = Column(Integer, ForeignKey("reservaciones.id"), nullable=False)
    monto = Column(Numeric(10, 2), nullable=False)
    metodo_pago = Column(SQLEnum(MetodoPago), nullable=False)
    estatus_pago = Column(SQLEnum(EstatusPago), default=EstatusPago.pendiente)
    fecha_pago = Column(DateTime, nullable=True)
    
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    # Relaciones
    reservacion = relationship("Reservacion", back_populates="pago")


class Calificacion(Base):
    __tablename__ = "calificaciones"

    id = Column(Integer, primary_key=True, autoincrement=True)
    viaje_id = Column(Integer, ForeignKey("viajes.id"), nullable=False)
    evaluador_id = Column(Integer, ForeignKey("usuarios.id"), nullable=False)
    evaluado_id = Column(Integer, ForeignKey("usuarios.id"), nullable=False)
    rol_evaluador = Column(SQLEnum(RolEvaluador), nullable=False)
    puntuacion = Column(Integer, nullable=False)
    comentarios = Column(Text, nullable=True)
    
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    # Relaciones
    viaje = relationship("Viaje", back_populates="calificaciones")
    evaluador = relationship("Usuario", foreign_keys=[evaluador_id], back_populates="calificaciones_emitidas")
    evaluado = relationship("Usuario", foreign_keys=[evaluado_id], back_populates="calificaciones_recibidas")
