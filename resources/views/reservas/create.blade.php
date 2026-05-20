@extends('layouts.app')

@section('title', 'Crear reserva')

@section('content')
<h1>Crear reserva</h1>
<form action="{{ route('reservas.store') }}" method="POST">
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
    <p>Entrada: <input type="date" name="fecha_entrada" value="{{ old('fecha_entrada') }}" required></p>
    <p>Salida: <input type="date" name="fecha_salida" value="{{ old('fecha_salida') }}" required></p>
    <p>Habitacion:
        <select name="habitacion_id" required>
            <option value="">Seleccione una habitación</option>
            @foreach($habitaciones as $habitacion)
                <option value="{{ $habitacion->id }}" {{ old('habitacion_id') == $habitacion->id ? 'selected' : '' }}>
                    {{ $habitacion->numero }} - {{ $habitacion->tipo }} - {{ $habitacion->estado }}
                </option>
            @endforeach
        </select>
    </p>
    <p>Servicio opcional:
        <select name="servicio_id">
            <option value="">Sin servicio</option>
            @foreach($servicios as $servicio)
                <option value="{{ $servicio->id }}" {{ old('servicio_id') == $servicio->id ? 'selected' : '' }}>
                    {{ $servicio->nombre }}
                </option>
            @endforeach
        </select>
    </p>
    <p>Cantidad servicio: <input type="number" name="cantidad" value="{{ old('cantidad', 1) }}" min="0"></p>
    <button type="submit">Guardar</button>
    <a href="{{ route('reservas.index') }}">Cancelar</a>
</form>
@endsection