@extends('layouts.app')

@section('title', 'Habitaciones')

@section('content')
<h1>Habitaciones</h1>
<a href="{{ route('habitaciones.create') }}">Crear habitacion</a>
<table border="1" cellpadding="6" cellspacing="0">
    <thead><tr><th>ID</th><th>Numero</th><th>Tipo</th><th>Precio</th><th>Estado</th><th>Activo</th><th>Opciones</th></tr></thead>
    <tbody>
        @forelse($habitaciones as $habitacion)
            <tr>
                <td>{{ $habitacion->id }}</td>
                <td>{{ $habitacion->numero }}</td>
                <td>{{ $habitacion->tipo }}</td>
                <td>{{ number_format((float) $habitacion->precio_por_noche, 2) }}</td>
                <td>{{ $habitacion->estado }}</td>
                <td>{{ $habitacion->activo ? 'Si' : 'No' }}</td>
                <td>
                    <a href="{{ route('habitaciones.edit', $habitacion->id) }}">Editar</a>
                    <form action="{{ route('habitaciones.destroy', $habitacion->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Eliminar registro?')">
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
