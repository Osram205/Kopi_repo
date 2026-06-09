<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateViajeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'origen' => ['sometimes', 'string', 'max:255'],
            'destino' => ['sometimes', 'string', 'max:255'],
            'fecha_salida' => ['sometimes', 'date', 'after_or_equal:today'],
            'hora_salida' => ['sometimes', 'date_format:H:i'],
            'asientos_disponibles' => ['sometimes', 'integer', 'min:0'],
            'costo_por_asiento' => ['sometimes', 'numeric', 'min:0'],
            'estatus' => ['sometimes', Rule::in(['programado', 'en_curso', 'completado', 'cancelado'])],
        ];
    }
}