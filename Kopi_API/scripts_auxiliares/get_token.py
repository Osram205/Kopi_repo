
import jwt
from datetime import datetime, timedelta
from app.core.config import settings

token_data = {"sub": "10"} # user id 10
expire = datetime.utcnow() + timedelta(minutes=30)
token_data.update({"exp": expire})
encoded_jwt = jwt.encode(token_data, settings.KOPI_SECRET_KEY, algorithm="HS256")
print(encoded_jwt)

