@extends('layouts.app')

@section('title', 'Habitaciones')

@section('content')
@include('partials.page-header', [
    'pageTitle' => 'Habitaciones',
    'pageSubtitle' => 'Inventario de habitaciones, precios, tipos y disponibilidad.',
    'pageActions' => '<a href="' . route('habitaciones.create') . '" class="hotel-btn-primary"><i data-lucide="plus"></i> Nueva habitación</a>',
])

<div class="hotel-table-wrap animate-hotel-fade">
    <table class="hotel-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Número</th>
                <th>Tipo</th>
                <th>Precio / noche</th>
                <th>Estado</th>
                <th>Activo</th>
                <th class="text-right">Opciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($habitaciones as $habitacion)
                <tr>
                    <td class="font-mono text-gray-500">#{{ $habitacion->id }}</td>
                    <td class="text-lg font-bold text-hotel-dark">{{ $habitacion->numero }}</td>
                    <td class="capitalize">{{ $habitacion->tipo }}</td>
                    <td class="font-semibold">${{ number_format((float) $habitacion->precio_por_noche, 2) }}</td>
                    <td>
                        @php
                            $estadoClass = match($habitacion->estado) {
                                'disponible' => 'hotel-badge-green',
                                'ocupada' => 'hotel-badge-amber',
                                'mantenimiento' => 'hotel-badge-red',
                                default => 'hotel-badge-gray',
                            };
                        @endphp
                        <span class="{{ $estadoClass }}">{{ $habitacion->estado }}</span>
                    </td>
                    <td>
                        @if($habitacion->activo)
                            <span class="hotel-badge-green">Sí</span>
                        @else
                            <span class="hotel-badge-gray">No</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('habitaciones.edit', $habitacion->id) }}" class="hotel-btn-secondary">
                                <i data-lucide="pencil"></i>
                                Editar
                            </a>
                            <form action="{{ route('habitaciones.destroy', $habitacion->id) }}" method="POST"
                                  data-confirm-delete="¿Eliminar la habitación {{ $habitacion->numero }}?">
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
                    <td colspan="7" class="py-12 text-center text-gray-500">
                        No hay habitaciones registradas.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
