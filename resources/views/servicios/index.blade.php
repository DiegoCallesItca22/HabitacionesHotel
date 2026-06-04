@extends('layouts.app')

@section('title', 'Servicios')

@section('content')
<h1>Servicios</h1>
<a href="{{ route('servicios.create') }}">Crear servicio</a>
<table border="1" cellpadding="6" cellspacing="0">
    <thead><tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Activo</th><th>Opciones</th></tr></thead>
    <tbody>
        @forelse($servicios as $servicio)
            <tr>
                <td>{{ $servicio->id }}</td>
                <td>{{ $servicio->nombre }}</td>
                <td>{{ number_format((float) $servicio->precio, 2) }}</td>
                <td>{{ $servicio->activo ? 'Si' : 'No' }}</td>
                <td>
                    <a href="{{ route('servicios.edit', $servicio->id) }}">Editar</a>
                    <form action="{{ route('servicios.destroy', $servicio->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Eliminar registro?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">No hay registros.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
