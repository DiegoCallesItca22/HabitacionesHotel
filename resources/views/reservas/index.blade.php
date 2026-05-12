@extends('layouts.app')

@section('title', 'Reservas')

@section('content')
<h1>Reservas</h1>
<a href="{{ route('reservas.create') }}">Crear reserva</a>
<table border="1" cellpadding="6" cellspacing="0">
    <thead><tr><th>ID</th><th>Usuario</th><th>Entrada</th><th>Salida</th><th>Estado</th><th>Total</th><th>Opciones</th></tr></thead>
    <tbody>
        @forelse($reservas as $reserva)
            <tr>
                <td>{{ $reserva->id }}</td>
                <td>{{ $reserva->usuario?->nombre }}</td>
                <td>{{ optional($reserva->fecha_entrada)->format('Y-m-d') }}</td>
                <td>{{ optional($reserva->fecha_salida)->format('Y-m-d') }}</td>
                <td>{{ $reserva->estado }}</td>
                <td>{{ number_format((float) $reserva->total, 2) }}</td>
                <td>
                    <a href="{{ route('reservas.edit', $reserva->id) }}">Editar</a>
                    <form action="{{ route('reservas.confirmar', $reserva->id) }}" method="POST" style="display:inline;">@csrf<button type="submit">Confirmar</button></form>
                    <form action="{{ route('reservas.cancelar', $reserva->id) }}" method="POST" style="display:inline;">@csrf<button type="submit">Cancelar</button></form>
                    <form action="{{ route('reservas.destroy', $reserva->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Eliminar registro?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7">No hay registros.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
