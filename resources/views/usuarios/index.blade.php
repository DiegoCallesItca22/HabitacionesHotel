@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<h1>Usuarios</h1>
<a href="{{ route('usuarios.create') }}">Crear usuario</a>
<table border="1" cellpadding="6" cellspacing="0">
    <thead>
        <tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Rol</th><th>Activo</th><th>Opciones</th></tr>
    </thead>
    <tbody>
        @forelse($usuarios as $usuario)
            <tr>
                <td>{{ $usuario->id }}</td>
                <td>{{ $usuario->nombre }}</td>
                <td>{{ $usuario->correo }}</td>
                <td>{{ $usuario->rol }}</td>
                <td>{{ $usuario->activo ? 'Si' : 'No' }}</td>
                <td>
                    <a href="{{ route('usuarios.edit', $usuario->id) }}">Editar</a>
                    <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Eliminar registro?')">
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
