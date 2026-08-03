import { apiClient } from '../api/client';
import { Viaje, ViajeCrear } from '../types/trip.types';

export const tripService = {
  listarViajes: async (): Promise<Viaje[]> => {
    const response = await apiClient.get<Viaje[]>('/viajes');
    return response.data;
  },
  obtenerViaje: async (id: string | number): Promise<Viaje> => {
    const response = await apiClient.get<Viaje>(`/viajes/${id}`);
    return response.data;
  },
  crearViaje: async (data: ViajeCrear): Promise<Viaje> => {
    const response = await apiClient.post<Viaje>('/viajes', data);
    return response.data;
  }
};
