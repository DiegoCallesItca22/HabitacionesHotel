@extends('layouts.app')

@section('title', 'Editar pago')

@section('content')
<h1>Editar pago</h1>
<form action="{{ route('pagos.update', $pago->id) }}" method="POST">
    @csrf
    @method('PUT')
    @include('pagos.partials.form', ['pago' => $pago])
    <button type="submit">Guardar</button>
    <a href="{{ route('pagos.index') }}">Cancelar</a>
</form>
@endsection
