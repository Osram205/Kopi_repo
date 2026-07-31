FROM python:3.11.7-slim

# Establecemos el directorio de trabajo
WORKDIR /app

# Ya no instalamos tesseract-ocr ni libgl1 porque el OCR fue desactivado
RUN apt-get update && apt-get install -y \
    gcc \
    && rm -rf /var/lib/apt/lists/*

# Copiamos primero el requirements.txt para aprovechar el caché de capas de Docker
COPY requirements.txt .

# Instalamos dependencias de Python
RUN pip install --no-cache-dir --upgrade pip && \
    pip install --no-cache-dir -r requirements.txt

# Copiamos el resto del código del API
COPY . .

# Exponemos el puerto
EXPOSE 8000

# Comando para arrancar el servidor
CMD ["uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "8000"]
