<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HabitacionesHotel - Iniciar sesión</title>
</head>
<body>
    <h1>Iniciar sesión</h1>

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <p>
            Correo: <input type="email" name="correo" value="{{ old('correo') }}" required>
        </p>
        <p>
            Contraseña: <input type="password" name="password" required>
        </p>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>