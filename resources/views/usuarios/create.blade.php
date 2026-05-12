@extends('layouts.app')

@section('title', 'Crear usuario')

@section('content')
<h1>Crear usuario</h1>
<form action="{{ route('usuarios.store') }}" method="POST">
    @csrf
    <p>Nombre: <input type="text" name="nombre" value="{{ old('nombre') }}" required></p>
    <p>Correo: <input type="email" name="correo" value="{{ old('correo') }}" required></p>
    <p>Password: <input type="password" name="password" required></p>
    <p>Rol:
        <select name="rol" required>
            <option value="admin">Admin</option>
            <option value="cliente">Cliente</option>
            <option value="recepcionista">Recepcionista</option>
        </select>
    </p>
    <p><label><input type="checkbox" name="activo" value="1" checked> Activo</label></p>
    <button type="submit">Guardar</button>
    <a href="{{ route('usuarios.index') }}">Cancelar</a>
</form>
@endsection
