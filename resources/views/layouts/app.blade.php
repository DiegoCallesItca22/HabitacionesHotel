<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HabitacionesHotel - @yield('title')</title>
</head>
<body>
    <nav>
        <a href="{{ route('home.home') }}">Inicio</a> |
        <a href="{{ route('usuarios.index') }}">Usuarios</a> |
        <a href="{{ route('habitaciones.index') }}">Habitaciones</a> |
        <a href="{{ route('servicios.index') }}">Servicios</a> |
        <a href="{{ route('reservas.index') }}">Reservas</a> |
        <a href="{{ route('detalle_reserva.index') }}">Detalle reserva</a> |
        <a href="{{ route('reserva_servicio.index') }}">Reserva servicio</a> |
        <a href="{{ route('facturas.index') }}">Facturas</a> |
        <a href="{{ route('pagos.index') }}">Pagos</a>
    </nav>

    <hr>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @yield('content')
</body>
</html>
