@extends('layouts.app')

@section('title', 'Crear reserva')

@section('content')
<h1>Crear reserva</h1>
<form action="{{ route('reservas.store') }}" method="POST" id="form-reserva">
    @csrf
    <p>Usuario (cliente):
        <select name="usuario_id" required>
            <option value="">Seleccione un cliente</option>
            @foreach($usuarios as $usuario)
                <option value="{{ $usuario->id }}" {{ old('usuario_id') == $usuario->id ? 'selected' : '' }}>
                    {{ $usuario->nombre }}
                </option>
            @endforeach
        </select>
    </p>
    <p>
        <span id="fechas-estado" style="display:block; font-weight:bold; margin-bottom:4px;"></span>
        Entrada: <input type="date" name="fecha_entrada" id="fecha_entrada" value="{{ old('fecha_entrada') }}" required>
        Salida: <input type="date" name="fecha_salida" id="fecha_salida" value="{{ old('fecha_salida') }}" required>
        <small>(la reserva se calcula por noches; la salida debe ser posterior a la entrada)</small>
    </p>
    <p>Habitacion:
        <select name="habitacion_id" required>
            <option value="">Seleccione una habitacion</option>
            @foreach($habitaciones as $habitacion)
                <option value="{{ $habitacion->id }}" data-precio="{{ $habitacion->precio_por_noche }}" {{ old('habitacion_id') == $habitacion->id ? 'selected' : '' }}>
                    {{ $habitacion->numero }} - {{ $habitacion->tipo }} — ${{ number_format((float) $habitacion->precio_por_noche, 2) }}/noche
                </option>
            @endforeach
        </select>
    </p>

    @include('reservas.partials.form-servicios', ['serviciosReserva' => []])

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
