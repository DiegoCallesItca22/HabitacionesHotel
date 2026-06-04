@extends('layouts.app')

@section('title', 'Crear pago')

@section('content')
<h1>Crear pago</h1>
<form action="{{ route('pagos.store') }}" method="POST">
    @csrf
    @include('pagos.partials.form')
    <button type="submit">Guardar</button>
    <a href="{{ route('pagos.index') }}">Cancelar</a>
</form>
@endsection
