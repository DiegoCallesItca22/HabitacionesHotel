@extends('layouts.app')

@section('title', 'Crear servicio')

@section('content')
<h1>Crear servicio</h1>
<form action="{{ route('servicios.store') }}" method="POST">
    @csrf
    <p>Nombre: <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="ej: Desayuno buffet" required></p>
    <p>Precio (USD): <input type="number" step="0.01" min="0" name="precio" value="{{ old('precio') }}" placeholder="ej: 15.00" required></p>
    <p><label><input type="checkbox" name="activo" value="1" checked> Activo</label></p>
    <button type="submit">Guardar</button>
    <a href="{{ route('servicios.index') }}">Cancelar</a>
</form>
@endsection
