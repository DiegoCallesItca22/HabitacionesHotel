@extends('layouts.app')

@section('title', 'Facturas')

@section('content')
<h1>Facturas</h1>
<a href="{{ route('facturas.create') }}">Crear factura</a>
<table border="1" cellpadding="6" cellspacing="0">
    <thead><tr><th>ID</th><th>Numero</th><th>Reserva</th><th>Subtotal</th><th>Impuestos</th><th>Total</th><th>Pagado</th><th>Saldo</th><th>Activo</th><th>Opciones</th></tr></thead>
    <tbody>
        @forelse($facturas as $factura)
            <tr>
                <td>{{ $factura->id }}</td>
                <td>{{ $factura->numero_factura }}</td>
                <td>{{ $factura->reserva_id }}</td>
                <td>{{ number_format((float) $factura->subtotal, 2) }}</td>
                <td>{{ number_format((float) $factura->impuestos, 2) }}</td>
                <td>{{ number_format((float) $factura->total, 2) }}</td>
                <td>{{ number_format((float) $factura->total_pagado, 2) }}</td>
                <td>{{ number_format((float) $factura->saldo_pendiente, 2) }}</td>
                <td>{{ $factura->activo ? 'Si' : 'No' }}</td>
                <td>
                    <a href="{{ route('facturas.edit', $factura->id) }}">Editar</a>
                    <form action="{{ route('facturas.destroy', $factura->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Eliminar registro?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="10">No hay registros.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
