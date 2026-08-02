import os
import sys

# Agregar la ruta del proyecto al sys.path para poder importar módulos
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from app.data.database import engine
from sqlalchemy import text

# Columnas a verificar e insertar
columns_to_add = [
    ("foto_credencial_frente", "VARCHAR(255) NULL DEFAULT NULL"),
    ("poliza_seguro", "VARCHAR(255) NULL DEFAULT NULL"),
    ("es_admin", "BOOLEAN NOT NULL DEFAULT 0"),
    ("token_recuperacion", "VARCHAR(100) NULL DEFAULT NULL"),
    ("codigo_otp", "VARCHAR(6) NULL DEFAULT NULL"),
    ("fcm_token", "VARCHAR(512) NULL DEFAULT NULL"),
    ("foto_licencia", "VARCHAR(255) NULL DEFAULT NULL"),
    ("tarjeta_circulacion", "VARCHAR(255) NULL DEFAULT NULL"),
    ("deleted_at", "DATETIME NULL DEFAULT NULL")
]

def fix_schema():
    with engine.connect() as conn:
        for col_name, col_type in columns_to_add:
            try:
                print(f"Intentando agregar columna {col_name}...")
                conn.execute(text(f"ALTER TABLE usuarios ADD COLUMN {col_name} {col_type}"))
                conn.commit()
                print(f"✅ Columna {col_name} agregada con éxito.")
            except Exception as e:
                if "Duplicate column name" in str(e):
                    print(f"⚡ Columna {col_name} ya existe. Ignorando...")
                else:
                    print(f"❌ Error al agregar {col_name}: {e}")

if __name__ == "__main__":
    fix_schema()
