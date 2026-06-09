<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreViajeRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a hacer esta petición.
     */
    public function authorize(): bool
    {
        // Devolvemos true porque la autorización real del vehículo
        // la tienes manejada en la lógica de negocio (o en un futuro Policy).
        return true; 
    }

    /**
     * Reglas de validación que se aplicarán a la petición.
     */
    public function rules(): array
    {
        return [
            'vehiculo_id' => ['required', 'integer', 'exists:vehiculos,id'],
            'origen' => ['required', 'string', 'max:255'],
            'destino' => ['required', 'string', 'max:255', 'different:origen'],
            'fecha_salida' => ['required', 'date', 'after_or_equal:today'],
            'hora_salida' => ['required', 'date_format:H:i'],
            'asientos_disponibles' => ['required', 'integer', 'min:1'],
            'costo_por_asiento' => ['required', 'numeric', 'min:0'],
            'paradas' => ['sometimes', 'array'],
            'paradas.*.nombre_parada' => ['required_with:paradas', 'string', 'max:150'],
            'paradas.*.coordenadas' => ['required_with:paradas', 'string', 'max:100'],
            'paradas.*.orden' => ['required_with:paradas', 'integer', 'min:1'],
        ];
    }

    /**
     * (Opcional) Mensajes personalizados de error para el frontend.
     */
    public function messages(): array
    {
        return [
            'destino.different' => 'El destino no puede ser igual al punto de origen.',
            'fecha_salida.after_or_equal' => 'La fecha de salida debe ser el día de hoy o una fecha futura.',
            'asientos_disponibles.min' => 'Debe haber al menos 1 asiento disponible.'
        ];
    }
}