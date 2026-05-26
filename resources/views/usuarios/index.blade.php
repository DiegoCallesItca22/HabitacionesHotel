@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
@include('partials.page-header', [
    'pageTitle' => 'Usuarios',
    'pageSubtitle' => 'Administración de cuentas, roles y acceso al sistema.',
    'pageActions' => '<a href="' . route('usuarios.create') . '" class="hotel-btn-primary"><i data-lucide="user-plus"></i> Crear usuario</a>',
])

<div class="hotel-table-wrap animate-hotel-fade">
    <table class="hotel-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Estado</th>
                <th class="text-right">Opciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usuarios as $usuario)
                <tr>
                    <td class="font-mono text-gray-500">#{{ $usuario->id }}</td>
                    <td class="font-semibold text-gray-900">{{ $usuario->nombre }}</td>
                    <td>{{ $usuario->correo }}</td>
                    <td>
                        @php
                            $rolClass = match($usuario->rol) {
                                'admin' => 'hotel-badge-green',
                                'recepcionista' => 'hotel-badge-blue',
                                default => 'hotel-badge-gray',
                            };
                        @endphp
                        <span class="{{ $rolClass }}">{{ $usuario->rol }}</span>
                    </td>
                    <td>
                        @if($usuario->activo)
                            <span class="hotel-badge-green">Activo</span>
                        @else
                            <span class="hotel-badge-red">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('usuarios.edit', $usuario->id) }}" class="hotel-btn-secondary">
                                <i data-lucide="pencil"></i>
                                Editar
                            </a>
                            <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST"
                                  data-confirm-delete="¿Eliminar al usuario {{ $usuario->nombre }}?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="hotel-btn-danger">
                                    <i data-lucide="trash-2"></i>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-gray-500">
                        No hay usuarios registrados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
