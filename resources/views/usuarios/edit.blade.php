@extends('layouts.app')

@section('title', 'Editar usuario')

@section('content')
<h1>Editar usuario</h1>
<form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
    @csrf
    @method('PUT')
    <p>Nombre: <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required></p>
    <p>Correo: <input type="email" name="correo" value="{{ old('correo', $usuario->correo) }}" required></p>
    <p>Password: <input type="password" name="password">
        <small>(dejar vacio para no cambiar; min. 8 caracteres, letras y numeros)</small>
    </p>
    <p>Rol:
        <select name="rol" required>
            <option value="admin" @selected($usuario->rol === 'admin')>Admin</option>
            <option value="cliente" @selected($usuario->rol === 'cliente')>Cliente</option>
            <option value="recepcionista" @selected($usuario->rol === 'recepcionista')>Recepcionista</option>
        </select>
    </p>
    <p><label><input type="checkbox" name="activo" value="1" @checked($usuario->activo)> Activo</label></p>
    <button type="submit">Guardar</button>
    <a href="{{ route('usuarios.index') }}">Cancelar</a>
</form>
@endsection
