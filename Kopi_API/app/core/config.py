from pydantic_settings import BaseSettings, SettingsConfigDict

class Settings(BaseSettings):
    PROJECT_NAME: str = "API de Kopi - Carpooling UPQ"
    VERSION: str = "1.0.0"
    
    # Credenciales tipadas
    SQLALCHEMY_DATABASE_URL: str
    KOPI_SECRET_KEY: str
    KOPI_TOKEN_EXPIRE_MINUTES: int = 1440
    STRIPE_SECRET_KEY: str | None = None
    KOPI_WEB_URL: str = "http://127.0.0.1:8001"
    FIREBASE_CREDENTIALS: str = "firebase-adminsdk.json"

    SMTP_HOST: str = "smtp.gmail.com"
    SMTP_PORT: int = 587
    SMTP_USER: str | None = None
    SMTP_PASSWORD: str | None = None
    SMTP_FROM_NAME: str = "KOPI - Movilidad Segura"

    # Esto le dice a Pydantic que lea el archivo .env
    model_config = SettingsConfigDict(env_file=".env", env_file_encoding="utf-8", extra="ignore")

settings = Settings()
