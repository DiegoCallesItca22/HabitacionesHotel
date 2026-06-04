@extends('layouts.app')

@section('title', 'Reserva servicio')

@section('content')
<h1>Reserva servicio</h1>
<table border="1" cellpadding="6" cellspacing="0">
    <thead><tr><th>ID</th><th>Reserva</th><th>Servicio</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr></thead>
    <tbody>
        @forelse($reservaServicios as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->reserva_id }}</td>
                <td>{{ $item->servicio?->nombre }}</td>
                <td>{{ $item->cantidad }}</td>
                <td>{{ number_format((float) $item->precio_unitario, 2) }}</td>
                <td>{{ number_format((float) $item->subtotal, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="6">No hay registros.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
