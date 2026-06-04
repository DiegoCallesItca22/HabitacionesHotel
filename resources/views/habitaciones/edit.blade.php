@extends('layouts.app')

@section('title', 'Editar habitacion')

@section('content')
<h1>Editar habitacion</h1>
<form action="{{ route('habitaciones.update', $habitacion->id) }}" method="POST">
    @csrf
    @method('PUT')
    <p>
        <span id="numero-estado" style="display:block; font-weight:bold; margin-bottom:4px;"></span>
        Numero: <input type="text" id="numero" name="numero" value="{{ old('numero', $habitacion->numero) }}" required autocomplete="off">
    </p>
    <p>Tipo: @include('habitaciones.partials.tipo', ['value' => $habitacion->tipo])</p>
    <p>Precio por noche (USD): <input type="number" step="0.01" min="0" name="precio_por_noche" placeholder="ej: 190.00" value="{{ old('precio_por_noche', $habitacion->precio_por_noche) }}" required></p>
    <p>Estado: @include('habitaciones.partials.estado', ['value' => $habitacion->estado])</p>
    <p><label><input type="checkbox" name="activo" value="1" @checked($habitacion->activo)> Activo</label></p>
    <button type="submit">Guardar</button>
    <a href="{{ route('habitaciones.index') }}">Cancelar</a>
</form>

<script>
(function () {
    const input = document.getElementById('numero');
    const estado = document.getElementById('numero-estado');
    const exceptId = @json($habitacion->id);
    const url = @json(route('habitaciones.verificar-numero'));
    let timer = null;

    function actualizarEstado() {
        const numero = input.value.trim();
        if (!numero) {
            estado.textContent = '';
            return;
        }

        clearTimeout(timer);
        timer = setTimeout(function () {
            fetch(url + '?numero=' + encodeURIComponent(numero) + '&except_id=' + exceptId)
                .then(r => r.json())
                .then(data => {
                    if (data.existe) {
                        estado.textContent = 'Este numero ya existe.';
                        estado.style.color = 'red';
                    } else {
                        estado.textContent = 'Numero disponible.';
                        estado.style.color = 'green';
                    }
                })
                .catch(() => { estado.textContent = ''; });
        }, 300);
    }

    input.addEventListener('input', actualizarEstado);
    actualizarEstado();
})();
</script>
@endsection
