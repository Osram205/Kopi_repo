# app/main.py
from fastapi import FastAPI
from app.data.database import engine, Base  # <-- CAMBIO: Importamos Base desde database directamente
from app.data import models                   # Mantenemos esta línea para que SQLAlchemy lea las tablas
from app.routers import auth, calificaciones, pagos, reservaciones, vehiculos, viajes

# CAMBIO: Usamos Base directamente
Base.metadata.create_all(bind=engine)

# Inicializamos la app con el nombre del proyecto
app = FastAPI(
    title="API de Kopi - Carpooling UPQ",
    description="Sistema backend para la gestión de viajes compartidos",
    version="1.0.0"
)

# Registramos nuestras rutas
app.include_router(auth.router)
app.include_router(vehiculos.router)
app.include_router(viajes.router)
app.include_router(reservaciones.router)
app.include_router(pagos.router)
app.include_router(calificaciones.router)

@app.get("/")
def raiz():
    return {"mensaje": "¡Bienvenido a la API de Kopi! El servidor está corriendo correctamente."}
