<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class MisViajes extends Component
{
    public $misReservas = [];
    public $modalPagoAbierto = false;
    public $reservacionSeleccionadaId;
    public $montoPago = 25.00; 
    public $metodo_pago = 'tarjeta'; 

    public function mount()
    {
        $token = Session::get('jwt_token');

        if (request()->query('pago_exitoso') == 'true') {
            $reserva_id = request()->query('reservacion_id');
            $metodo = request()->query('metodo');
            $session_id = request()->query('session_id'); // <-- ATRAPAMOS EL ID CRIPTOGRÁFICO DE STRIPE

            // Enviamos la prueba a FastAPI para que audite
            $response = Http::withToken($token)->post(config('services.fastapi.url') . '/pagos/confirmar/', [
                'reservacion_id' => (int) $reserva_id,
                'metodo_pago' => $metodo,
                'session_id' => $session_id // <-- LO ENVIAMOS AL BACKEND
            ]);

            if ($response->successful()) {
                session()->flash('success', '¡Pago auditado y procesado con éxito por Stripe! Tu viaje está 100% asegurado.');
            } else {
                session()->flash('error', '⚠️ Error de validación bancaria: No se pudo verificar la autenticidad del pago.');
            }
            return redirect()->route('viajes.mios');
        }

        if (request()->query('pago_cancelado') == 'true') {
            session()->flash('error', 'El pago fue cancelado en el portal bancario.');
            return redirect()->route('viajes.mios');
        }

        $this->cargarViajes();
    }

    public function cargarViajes()
    {
        $token = Session::get('jwt_token');
        $response = Http::withToken($token)->get(config('services.fastapi.url') . '/reservaciones/?rol=pasajero');

        if ($response->successful()) {
            $this->misReservas = $response->json();
        }
    }

    public function abrirModalPago($reservacionId)
    {
        $this->reservacionSeleccionadaId = $reservacionId;
        $this->modalPagoAbierto = true;
    }

    public function cerrarModal()
    {
        $this->modalPagoAbierto = false;
        $this->reset(['reservacionSeleccionadaId']);
    }

    public function procesarPago()
    {
        $token = Session::get('jwt_token');

        try {
            // Pedimos el túnel de cobro a FastAPI
            $response = Http::withToken($token)->post(config('services.fastapi.url') . '/pagos/checkout/', [
                'reservacion_id' => (int) $this->reservacionSeleccionadaId,
                'monto' => (float) $this->montoPago,
                'metodo_pago' => $this->metodo_pago
            ]);

            if ($response->successful()) {
                // REDIRECCIÓN MAESTRA: Lanzamos al usuario al portal bancario de Stripe
                $urlStripe = $response->json()['checkout_url'];
                return redirect()->away($urlStripe); 
            } else {
                $errDetail = $response->json()['detail'] ?? 'Los servidores financieros están ocupados.';
                $errorMsg = is_array($errDetail) ? (isset($errDetail[0]['msg']) ? $errDetail[0]['msg'] : json_encode($errDetail)) : $errDetail;
                session()->flash('error', $errorMsg);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al conectar con la pasarela de pagos.');
        }
    }

    public function render()
    {
        return view('livewire.mis-viajes')->layout('layouts.app');
    }
}