@extends('layouts.app')

@section('title', 'Crear habitacion')

@section('content')
<h1>Crear habitacion</h1>
<form action="{{ route('habitaciones.store') }}" method="POST">
    @csrf
    <p>Numero: <input type="text" name="numero" value="{{ old('numero') }}" required></p>
    <p>Tipo: @include('habitaciones.partials.tipo')</p>
    <p>Precio por noche: <input type="number" step="0.01" name="precio_por_noche" value="{{ old('precio_por_noche') }}" required></p>
    <p>Estado: @include('habitaciones.partials.estado')</p>
    <p><label><input type="checkbox" name="activo" value="1" checked> Activo</label></p>
    <button type="submit">Guardar</button>
    <a href="{{ route('habitaciones.index') }}">Cancelar</a>
</form>
@endsection
