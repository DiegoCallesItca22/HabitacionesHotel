@extends('layouts.app')

@section('title', 'Servicios')

@section('content')
@include('partials.page-header', [
    'pageTitle' => 'Servicios',
    'pageSubtitle' => 'Catálogo de servicios adicionales ofrecidos por el hotel.',
    'pageActions' => '<a href="' . route('servicios.create') . '" class="hotel-btn-primary"><i data-lucide="plus"></i> Nuevo servicio</a>',
])

<div class="hotel-table-wrap animate-hotel-fade">
    <table class="hotel-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Estado</th>
                <th class="text-right">Opciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($servicios as $servicio)
                <tr>
                    <td class="font-mono text-gray-500">#{{ $servicio->id }}</td>
                    <td class="font-semibold text-gray-900">{{ $servicio->nombre }}</td>
                    <td class="font-semibold text-hotel-dark">${{ number_format((float) $servicio->precio, 2) }}</td>
                    <td>
                        @if($servicio->activo)
                            <span class="hotel-badge-green">Activo</span>
                        @else
                            <span class="hotel-badge-gray">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('servicios.edit', $servicio->id) }}" class="hotel-btn-secondary">
                                <i data-lucide="pencil"></i>
                                Editar
                            </a>
                            <form action="{{ route('servicios.destroy', $servicio->id) }}" method="POST"
                                  data-confirm-delete="¿Eliminar el servicio {{ $servicio->nombre }}?">
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
                    <td colspan="5" class="py-12 text-center text-gray-500">
                        No hay servicios registrados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
