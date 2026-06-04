@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<h1>Resumen del sistema</h1>
<ul>
    <li>Usuarios: {{ $stats['usuarios'] }}</li>
    <li>Habitaciones: {{ $stats['habitaciones'] }}</li>
    <li>Reservas: {{ $stats['reservas'] }}</li>
    <li>Facturas: {{ $stats['facturas'] }}</li>
    <li>Pagos completados: ${{ number_format((float) $stats['pagos'], 2) }}</li>
</ul>
@endsection
