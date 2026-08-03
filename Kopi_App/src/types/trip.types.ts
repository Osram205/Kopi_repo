export interface Parada {
  id?: string | number;
  ubicacion: string;
  orden?: number;
}

export interface Conductor {
  id?: string | number;
  nombre: string;
  calificacion?: number;
}

export interface Vehiculo {
  id?: string | number;
  marca: string;
  placa: string;
}

export interface Viaje {
  id: string | number;
  origen: string;
  destino: string;
  fecha_salida: string;
  hora_salida: string;
  asientos_disponibles: number;
  costo_por_asiento: number;
  paradas: Parada[];
  conductor?: Conductor;
  vehiculo?: Vehiculo;
  estado?: string;
  // Extras for frontend compatibility
  imageUrl?: string;
  description?: string;
}

export interface ViajeCrear {
  origen: string;
  destino: string;
  fecha_salida: string;
  hora_salida: string;
  asientos_disponibles: number;
  costo_por_asiento: number;
  paradas?: string[];
  vehiculo_id: number;
}
