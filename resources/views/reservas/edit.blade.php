@extends('layouts.app')

@section('title', 'Editar reserva')

@section('content')
<h1>Editar reserva</h1>
<form action="{{ route('reservas.update', $reserva->id) }}" method="POST">
    @csrf
    @method('PUT')
    <p>Usuario:
        <select name="usuario_id" required>
            @foreach($usuarios as $usuario)
                <option value="{{ $usuario->id }}" @selected($usuario->id == $reserva->usuario_id)>{{ $usuario->nombre }}</option>
            @endforeach
        </select>
    </p>
    <p>Entrada: <input type="date" name="fecha_entrada" value="{{ old('fecha_entrada', optional($reserva->fecha_entrada)->format('Y-m-d')) }}" required></p>
    <p>Salida: <input type="date" name="fecha_salida" value="{{ old('fecha_salida', optional($reserva->fecha_salida)->format('Y-m-d')) }}" required></p>
    <p>Estado:
        <select name="estado" required>
            @foreach(['pendiente', 'confirmada', 'cancelada', 'completada'] as $estado)
                <option value="{{ $estado }}" @selected($reserva->estado === $estado)>{{ $estado }}</option>
            @endforeach
        </select>
    </p>
    <p><label><input type="checkbox" name="activo" value="1" @checked($reserva->activo)> Activo</label></p>
    <button type="submit">Guardar</button>
    <a href="{{ route('reservas.index') }}">Cancelar</a>
</form>
@endsection
