# Kopi_Admin es una aplicación Flask (Python), no una app Node (Angular/React).
# Por lo tanto, no se utiliza Node ni se sirve estáticamente con Nginx de manera pura.
# Debe ejecutarse con Gunicorn o el servidor interno de Flask.

FROM python:3.11.7-slim

WORKDIR /app

# Archivo original requirements.txt de la app Admin
COPY requirements.txt .

# Instalamos gunicorn para producción y fijamos versión
RUN pip install --no-cache-dir -r requirements.txt \
    && pip install --no-cache-dir gunicorn==21.2.0

COPY . .

EXPOSE 5000

# Usar Gunicorn en lugar del servidor de desarrollo de Flask
CMD ["gunicorn", "-w", "2", "-b", "0.0.0.0:5000", "app:app"]
