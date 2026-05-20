<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HabitacionesHotel — @yield('title')</title>
</head>
<body>
    <nav>
        <a href="{{ route('home.home') }}">Inicio</a> |

        @if(Auth::check() && Auth::user()->rol === 'admin')
            <a href="{{ route('metricas.index') }}">📊 Métricas</a> |
            <a href="{{ route('usuarios.index') }}">Usuarios</a> |
        @endif

        <a href="{{ route('habitaciones.index') }}">Habitaciones</a> |
        <a href="{{ route('servicios.index') }}">Servicios</a> |
        <a href="{{ route('reservas.index') }}">Reservas</a> |
        <a href="{{ route('detalle_reserva.index') }}">Detalle reserva</a> |
        <a href="{{ route('reserva_servicio.index') }}">Reserva servicio</a> |

        @if(Auth::check() && Auth::user()->rol === 'admin')
            <a href="{{ route('facturas.index') }}">Facturas</a> |
            <a href="{{ route('pagos.index') }}">Pagos</a> |
        @endif

        @if(Auth::check())
            <span style="color:#555;">{{ Auth::user()->nombre }} ({{ Auth::user()->rol }})</span> &nbsp;
            <form action="{{ route('logout') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit">Cerrar sesión</button>
            </form>
        @endif
    </nav>

    <hr>

    @if(session('success'))
        <p style="color:green; font-weight:bold;">✅ {{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p style="color:red; font-weight:bold;">❌ {{ session('error') }}</p>
    @endif

    @if($errors->any())
        <ul style="color:red;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @yield('content')
</body>
</html>