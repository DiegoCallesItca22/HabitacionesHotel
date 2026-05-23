@extends('layouts.app')

@section('title', 'Editar reserva')

@section('content')
<h1>Editar reserva</h1>
<form action="{{ route('reservas.update', $reserva->id) }}" method="POST" id="form-reserva">
    @csrf
    @method('PUT')
    <p>Usuario:
        <select name="usuario_id" required>
            @foreach($usuarios as $usuario)
                <option value="{{ $usuario->id }}" @selected($usuario->id == $reserva->usuario_id)>{{ $usuario->nombre }}</option>
            @endforeach
        </select>
    </p>
    <p>
        <span id="fechas-estado" style="display:block; font-weight:bold; margin-bottom:4px;"></span>
        Entrada: <input type="date" name="fecha_entrada" id="fecha_entrada" value="{{ old('fecha_entrada', optional($reserva->fecha_entrada)->format('Y-m-d')) }}" required>
        Salida: <input type="date" name="fecha_salida" id="fecha_salida" value="{{ old('fecha_salida', optional($reserva->fecha_salida)->format('Y-m-d')) }}" required>
        <small>(reserva por noches)</small>
    </p>
    <p>Habitacion:
        <select name="habitacion_id" required>
            @foreach($habitaciones as $habitacion)
                <option value="{{ $habitacion->id }}" @selected($detalle && $detalle->habitacion_id == $habitacion->id)>
                    {{ $habitacion->numero }} - {{ $habitacion->tipo }} — ${{ number_format((float) $habitacion->precio_por_noche, 2) }}/noche
                </option>
            @endforeach
        </select>
    </p>
    <p>Estado:
        <select name="estado" required>
            @foreach(['pendiente', 'confirmada', 'cancelada', 'completada'] as $estado)
                <option value="{{ $estado }}" @selected($reserva->estado === $estado)>{{ $estado }}</option>
            @endforeach
        </select>
    </p>

    @include('reservas.partials.form-servicios', [
        'serviciosReserva' => ($serviciosReserva ?? collect())->map(fn ($s) => [
            'servicio_id' => $s['servicio_id'],
            'precio' => $s['precio'],
            'cantidad' => $s['cantidad'],
        ])->values(),
    ])

    <button type="submit">Guardar</button>
    <a href="{{ route('reservas.index') }}">Cancelar</a>
</form>

<script>
(function () {
    const entrada = document.getElementById('fecha_entrada');
    const salida = document.getElementById('fecha_salida');
    const estado = document.getElementById('fechas-estado');

    function validarFechas() {
        if (!entrada.value || !salida.value) {
            estado.textContent = '';
            return;
        }
        const e = new Date(entrada.value + 'T00:00:00');
        const s = new Date(salida.value + 'T00:00:00');
        const noches = Math.round((s - e) / (1000 * 60 * 60 * 24));
        if (noches < 1) {
            estado.textContent = 'Debe haber al menos 1 noche entre entrada y salida.';
            estado.style.color = 'red';
        } else {
            estado.textContent = noches + ' noche(s) de estadia.';
            estado.style.color = 'green';
        }
    }

    entrada.addEventListener('change', validarFechas);
    salida.addEventListener('change', validarFechas);
    validarFechas();
})();
</script>
@endsection
