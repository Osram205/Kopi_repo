<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class PublicarViaje extends Component
{
    // Propiedades del formulario
    public $origen;
    public $origen_lat = '20.5931'; // Querétaro por defecto
    public $origen_lng = '-100.3920';
    public $destino;
    public $destino_lat = '20.5931';
    public $destino_lng = '-100.3920';
    public $fecha_salida;
    public $hora_salida;
    public $asientos_disponibles;
    public $costo_por_asiento;
    public $vehiculo_id;
    
    public $paradas_intermedias = [];
    
    // Viajes Recurrentes
    public $es_recurrente = false;
    public $dias_recurrentes = [];
    public $semanas_recurrentes = 1;

    public $vehiculos = [];

    public function mount()
    {
        if (!Session::get('es_conductor') && Session::get('estatus_verificacion') !== 'aprobado') {
            return redirect()->route('viajes.index')->with('error', 'Acceso denegado. No tienes permisos de conductor autorizado.');
        }

        $token = Session::get('jwt_token');
        
        // 1. Obtenemos los vehículos del conductor logueado
        $response = Http::withToken($token)->get(config('services.fastapi.url') . '/vehiculos/');

        if ($response->successful()) {
            $this->vehiculos = $response->json();
            // Seleccionamos automáticamente el primer vehículo registrado
            if (count($this->vehiculos) > 0) {
                $this->vehiculo_id = $this->vehiculos[0]['id'];
            }
        }
    }

    public function addParada()
    {
        $this->paradas_intermedias[] = ['nombre' => ''];
    }

    public function removeParada($index)
    {
        unset($this->paradas_intermedias[$index]);
        $this->paradas_intermedias = array_values($this->paradas_intermedias);
    }

    public function publicar()
    {
        // 2. Validaciones locales en Laravel
        $this->validate([
            'vehiculo_id' => 'required|integer',
            'origen' => 'required|string|max:100',
            'destino' => 'required|string|max:100',
            'fecha_salida' => 'required|date|after_or_equal:today',
            'hora_salida' => 'required',
            'asientos_disponibles' => 'required|integer|min:1|max:7',
            'costo_por_asiento' => 'required|numeric|min:0',
        ]);

        $token = Session::get('jwt_token');

        $basePayload = [
            'vehiculo_id' => (int) $this->vehiculo_id,
            'origen' => $this->origen,
            'destino' => $this->destino,
            'hora_salida' => $this->hora_salida . ':00', 
            'asientos_disponibles' => (int) $this->asientos_disponibles,
            'costo_por_asiento' => (float) $this->costo_por_asiento,
            'paradas' => []
        ];

        $basePayload['paradas'][] = [
            'nombre_parada' => 'Punto de Encuentro: ' . $this->origen,
            'coordenadas' => $this->origen_lat . ',' . $this->origen_lng,
            'orden' => 1
        ];

        $orden = 2;
        foreach ($this->paradas_intermedias as $parada) {
            if (!empty($parada['nombre'])) {
                $basePayload['paradas'][] = [
                    'nombre_parada' => 'Parada: ' . $parada['nombre'],
                    'coordenadas' => $this->origen_lat . ',' . $this->origen_lng,
                    'orden' => $orden
                ];
                $orden++;
            }
        }

        $basePayload['paradas'][] = [
            'nombre_parada' => 'Destino Final: ' . $this->destino,
            'coordenadas' => $this->destino_lat . ',' . $this->destino_lng,
            'orden' => $orden
        ];

        $fechas_a_crear = [$this->fecha_salida];

        if ($this->es_recurrente && count($this->dias_recurrentes) > 0) {
            $fechas_a_crear = [];
            $startDate = \Carbon\Carbon::parse($this->fecha_salida);
            $endDate = $startDate->copy()->addWeeks($this->semanas_recurrentes);
            
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                if (in_array($date->dayOfWeekIso, $this->dias_recurrentes)) {
                    $fechas_a_crear[] = $date->format('Y-m-d');
                }
            }
        }

        $todosExitosos = true;
        foreach ($fechas_a_crear as $fecha) {
            $payload = $basePayload;
            $payload['fecha_salida'] = $fecha;
            $response = Http::withToken($token)->post(config('services.fastapi.url') . '/viajes/', $payload);
            if (!$response->successful()) {
                $todosExitosos = false;
            }
        }

        if ($todosExitosos) {
            session()->flash('success', '¡Tu(s) viaje(s) ha(n) sido publicado(s) exitosamente!');
            return redirect()->route('viajes.index');
        } else {
            session()->flash('error', 'Hubo un error al publicar algunos viajes de tu rutina.');
        }


    }

    public function render()
    {
        return view('livewire.publicar-viaje')->layout('layouts.app');
    }
}