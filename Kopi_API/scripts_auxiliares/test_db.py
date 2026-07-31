
import sys
import os
sys.path.append(os.path.dirname(os.path.abspath('__file__')))
from app.data import database, models
db = database.SessionLocal()
viajes = db.query(models.Viaje).all()
print('Total viajes en base de datos:', len(viajes))
for v in viajes:
    print(f'ID: {v.id}, Estatus: {v.estatus}, Asientos: {v.asientos_disponibles}, Borrado: {v.deleted_at}')

