@extends('layouts.app')

@section('title', 'Crear factura')

@section('content')
<h1>Crear factura</h1>
<form action="{{ route('facturas.store') }}" method="POST">
    @csrf
    <p>Reserva:
        <select name="reserva_id" required>
            @foreach($reservas as $reserva)
                <option value="{{ $reserva->id }}">Reserva #{{ $reserva->id }}</option>
            @endforeach
        </select>
    </p>
    <p>Impuestos: <input type="number" step="0.01" name="impuestos" value="{{ old('impuestos') }}"></p>
    <button type="submit">Guardar</button>
    <a href="{{ route('facturas.index') }}">Cancelar</a>
</form>
@endsection
