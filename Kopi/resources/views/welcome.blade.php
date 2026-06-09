@if(session('error'))
    <div style="background-color: red; color: white; padding: 10px;">
        {{ session('error') }}
    </div>
@endif

<h2>Viajes Disponibles</h2>
<ul>
    @forelse($viajes as $viaje)
        <li>De {{ $viaje['origen'] }} a {{ $viaje['destino'] }} - {{ $viaje['fecha_salida'] }}</li>
    @empty
        <li>No hay viajes disponibles en este momento.</li>
    @endforelse
</ul>