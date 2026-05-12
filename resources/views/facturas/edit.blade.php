@extends('layouts.app')

@section('title', 'Editar factura')

@section('content')
<h1>Editar factura</h1>
<form action="{{ route('facturas.update', $factura->id) }}" method="POST">
    @csrf
    @method('PUT')
    <p>Numero: {{ $factura->numero_factura }}</p>
    <p>Total: {{ number_format((float) $factura->total, 2) }}</p>
    <p><label><input type="checkbox" name="activo" value="1" @checked($factura->activo)> Activo</label></p>
    <button type="submit">Guardar</button>
    <a href="{{ route('facturas.index') }}">Cancelar</a>
</form>
@endsection
