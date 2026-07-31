
import shutil
import os
from sqlalchemy import create_engine, text

shutil.copyfile("D:/Kopi/Kopi_repo/Kopi_Web/public/favicon.ico", "D:/Kopi/Kopi_repo/Kopi_API/static/uploads/default_perfil.png")

# Update database
engine = create_engine("mysql+pymysql://root:Valeos0430!@127.0.0.1:3306/Kopi")
with engine.connect() as conn:
    conn.execute(text("UPDATE usuarios SET foto_perfil = \"default_perfil.png\""))
    conn.commit()
print("All users updated with default_perfil.png")

