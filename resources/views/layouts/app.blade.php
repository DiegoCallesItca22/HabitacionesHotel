<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    @include('partials.head')
</head>
<body class="min-h-full bg-gray-100 font-sans text-gray-800 antialiased">
    <header class="border-b-2 border-hotel-mid bg-hotel-dark text-white shadow-md">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <a href="{{ route('home.home') }}" class="group flex items-center gap-3 no-underline">
                <div class="flex h-11 w-11 items-center justify-center border-2 border-hotel-light bg-hotel-mid text-lg font-bold tracking-tight transition-transform duration-200 group-hover:scale-105">
                    ZZ
                </div>
                <div>
                    <span class="block text-lg font-bold leading-tight">Hotel Zizu</span>
                    <span class="text-xs uppercase tracking-widest text-hotel-muted">Gestión de habitaciones</span>
                </div>
            </a>

            @auth
            <div class="flex flex-wrap items-center gap-3 text-sm">
                <span class="hidden border border-white/20 bg-white/5 px-3 py-1.5 text-xs uppercase tracking-wide text-gray-200 sm:inline">
                    {{ Auth::user()->nombre }}
                    <span class="text-hotel-muted">· {{ Auth::user()->rol }}</span>
                </span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="hotel-btn border-white/30 bg-transparent text-white hover:border-white hover:bg-white/10">
                        <i data-lucide="log-out"></i>
                        Salir
                    </button>
                </form>
            </div>
            @endauth
        </div>

        @auth
        <nav class="border-t border-white/10 bg-hotel-dark/95">
            <div class="mx-auto flex max-w-7xl flex-wrap gap-1 px-4 py-2 sm:px-6 lg:px-8">
                <a href="{{ route('home.home') }}" data-nav-link
                   class="border-2 border-transparent px-4 py-2 text-sm font-semibold uppercase tracking-wide text-gray-300 transition-colors hover:border-white/20 hover:bg-white/5 hover:text-white">
                    Inicio
                </a>

                @if(Auth::user()->rol === 'admin')
                    <a href="{{ route('metricas.index') }}" data-nav-link
                       class="border-2 border-transparent px-4 py-2 text-sm font-semibold uppercase tracking-wide text-gray-300 transition-colors hover:border-white/20 hover:bg-white/5 hover:text-white">
                        Métricas
                    </a>
                    <a href="{{ route('usuarios.index') }}" data-nav-link
                       class="border-2 border-transparent px-4 py-2 text-sm font-semibold uppercase tracking-wide text-gray-300 transition-colors hover:border-white/20 hover:bg-white/5 hover:text-white">
                        Usuarios
                    </a>
                @endif

                @if(in_array(Auth::user()->rol, ['admin', 'recepcionista']))
                    <a href="{{ route('habitaciones.index') }}" data-nav-link
                       class="border-2 border-transparent px-4 py-2 text-sm font-semibold uppercase tracking-wide text-gray-300 transition-colors hover:border-white/20 hover:bg-white/5 hover:text-white">
                        Habitaciones
                    </a>
                    <a href="{{ route('servicios.index') }}" data-nav-link
                       class="border-2 border-transparent px-4 py-2 text-sm font-semibold uppercase tracking-wide text-gray-300 transition-colors hover:border-white/20 hover:bg-white/5 hover:text-white">
                        Servicios
                    </a>
                @endif
            </div>
        </nav>
        @endauth
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @include('partials.alerts')
        @yield('content')
    </main>

    <footer class="mt-auto border-t-2 border-gray-200 bg-white py-4 text-center text-xs text-gray-500">
        Hotel Zizu &copy; {{ date('Y') }} — Panel administrativo
    </footer>

    @stack('scripts')
</body>
</html>
