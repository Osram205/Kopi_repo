# app/main.py
from fastapi import FastAPI
from app.data.database import engine, Base
from app.data import models
from app.core.config import settings # <-- IMPORTACIÓN NUEVA
from app.routers import auth, calificaciones, pagos, reservaciones, vehiculos, viajes, admin

Base.metadata.create_all(bind=engine)

# <-- CAMBIO: Usamos las variables centralizadas
app = FastAPI(
    title=settings.PROJECT_NAME,
    description="Sistema backend para la gestión de viajes compartidos",
    version=settings.VERSION
)

app.include_router(auth.router)
app.include_router(vehiculos.router)
app.include_router(viajes.router)
app.include_router(reservaciones.router)
app.include_router(pagos.router)
app.include_router(calificaciones.router)
app.include_router(admin.router)

@app.get("/")
def raiz():
    # <-- CAMBIO: Actualizamos el mensaje de retorno
    return {"mensaje": f"¡Bienvenido a {settings.PROJECT_NAME}! El servidor está corriendo correctamente."}