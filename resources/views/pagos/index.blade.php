@extends('layouts.app')

@section('title', 'Pagos')

@section('content')
<h1>Pagos</h1>
<a href="{{ route('pagos.create') }}">Crear pago</a>
<table border="1" cellpadding="6" cellspacing="0">
    <thead><tr><th>ID</th><th>Factura</th><th>Monto</th><th>Metodo</th><th>Estado</th><th>Opciones</th></tr></thead>
    <tbody>
        @forelse($pagos as $pago)
            <tr>
                <td>{{ $pago->id }}</td>
                <td>{{ $pago->factura?->numero_factura }}</td>
                <td>{{ number_format((float) $pago->monto, 2) }}</td>
                <td>{{ $pago->metodo_pago }}</td>
                <td>{{ $pago->estado_pago }}</td>
                <td>
                    <a href="{{ route('pagos.edit', $pago->id) }}">Editar</a>
                    <form action="{{ route('pagos.procesar', $pago->id) }}" method="POST" style="display:inline;">@csrf<button type="submit">Procesar</button></form>
                    <form action="{{ route('pagos.cancelar', $pago->id) }}" method="POST" style="display:inline;">@csrf<button type="submit">Cancelar</button></form>
                    <form action="{{ route('pagos.reembolsar', $pago->id) }}" method="POST" style="display:inline;">@csrf<button type="submit">Reembolsar</button></form>
                    <form action="{{ route('pagos.destroy', $pago->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Eliminar registro?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">No hay registros.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
