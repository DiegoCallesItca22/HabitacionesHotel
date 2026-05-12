@extends('layouts.app')

@section('title', 'Editar habitacion')

@section('content')
<h1>Editar habitacion</h1>
<form action="{{ route('habitaciones.update', $habitacion->id) }}" method="POST">
    @csrf
    @method('PUT')
    <p>Numero: <input type="text" name="numero" value="{{ old('numero', $habitacion->numero) }}" required></p>
    <p>Tipo: @include('habitaciones.partials.tipo', ['value' => $habitacion->tipo])</p>
    <p>Precio por noche: <input type="number" step="0.01" name="precio_por_noche" value="{{ old('precio_por_noche', $habitacion->precio_por_noche) }}" required></p>
    <p>Estado: @include('habitaciones.partials.estado', ['value' => $habitacion->estado])</p>
    <p><label><input type="checkbox" name="activo" value="1" @checked($habitacion->activo)> Activo</label></p>
    <button type="submit">Guardar</button>
    <a href="{{ route('habitaciones.index') }}">Cancelar</a>
</form>
@endsection
