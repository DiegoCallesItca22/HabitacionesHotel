@extends('layouts.app')

@section('title', 'Detalle reserva')

@section('content')
<h1>Detalle reserva</h1>
<table border="1" cellpadding="6" cellspacing="0">
    <thead><tr><th>ID</th><th>Reserva</th><th>Habitacion</th><th>Noches</th><th>Precio noche</th><th>Subtotal</th></tr></thead>
    <tbody>
        @forelse($detalles as $detalle)
            <tr>
                <td>{{ $detalle->id }}</td>
                <td>{{ $detalle->reserva_id }}</td>
                <td>{{ $detalle->habitacion?->numero }}</td>
                <td>{{ $detalle->noches }}</td>
                <td>{{ number_format((float) $detalle->precio_noche, 2) }}</td>
                <td>{{ number_format((float) $detalle->subtotal, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="6">No hay registros.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
