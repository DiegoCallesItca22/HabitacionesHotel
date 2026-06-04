@extends('layouts.app')

@section('title', 'Reservas')

@section('content')
<h1>Reservas</h1>
<a href="{{ route('reservas.create') }}">Crear reserva</a>
<table border="1" cellpadding="6" cellspacing="0">
    <thead><tr><th>ID</th><th>Usuario</th><th>Entrada</th><th>Salida</th><th>Estado</th><th>Total (USD)</th><th>Opciones</th></tr></thead>
    <tbody>
        @forelse($reservas as $reserva)
            <tr>
                <td>{{ $reserva->id }}</td>
                <td>{{ $reserva->usuario?->nombre }}</td>
                <td>{{ optional($reserva->fecha_entrada)->format('Y-m-d') }}</td>
                <td>{{ optional($reserva->fecha_salida)->format('Y-m-d') }}</td>
                <td>{{ $reserva->estado }}</td>
                <td>${{ number_format($reserva->total, 2) }}</td>
                <td>
                    <a href="{{ route('reservas.edit', $reserva->id) }}">Editar</a>
                    @if($reserva->estado === 'pendiente')
                        <form action="{{ route('reservas.confirmar', $reserva->id) }}" method="POST" style="display:inline;">@csrf<button type="submit">Confirmar</button></form>
                    @endif
                    @if(in_array($reserva->estado, ['pendiente', 'confirmada']))
                        <form action="{{ route('reservas.cancelar', $reserva->id) }}" method="POST" style="display:inline;">@csrf<button type="submit">Cancelar</button></form>
                    @endif
                    @if($reserva->estado === 'cancelada')
                        <form action="{{ route('reservas.destroy', $reserva->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Desactivar esta reserva?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Eliminar</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7">No hay registros.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
