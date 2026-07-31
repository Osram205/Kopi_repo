
from sqlalchemy import create_engine, text
engine = create_engine("mysql+pymysql://root:Valeos0430!@127.0.0.1:3306/Kopi")
with engine.connect() as conn:
    result = conn.execute(text("SELECT id, nombre, telefono, foto_perfil FROM usuarios"))
    for row in result:
        print(row)

