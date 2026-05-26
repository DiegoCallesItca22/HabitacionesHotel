@extends('layouts.app')

@section('title', 'Pagos')

@section('content')
@include('partials.page-header', [
    'pageTitle' => 'Pagos',
    'pageSubtitle' => 'Administración de transacciones, métodos de pago y estados de cobros.',
    'pageActions' => '<a href="' . route('pagos.create') . '" class="hotel-btn-primary"><i data-lucide="plus"></i> Registrar pago</a>',
])

<div class="hotel-table-wrap animate-hotel-fade">
    <table class="hotel-table">
        <thead>
            <tr>
                <th>ID Pago</th>
                <th>Factura</th>
                <th>Monto (USD)</th>
                <th>Método</th>
                <th>Estado</th>
                <th class="text-right">Opciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pagos as $pago)
                <tr>
                    <td class="font-mono text-gray-500">#{{ $pago->id }}</td>
                    <td class="font-semibold text-gray-700 font-mono">{{ $pago->factura?->numero_factura ?? 'Sin Factura' }}</td>
                    <td class="font-bold text-hotel-dark font-mono">${{ number_format((float) $pago->monto, 2) }}</td>
                    <td class="capitalize">{{ str_replace('_', ' ', $pago->metodo_pago) }}</td>
                    <td>
                        @php
                            $estadoClass = match($pago->estado_pago) {
                                'pendiente' => 'hotel-badge-amber',
                                'completado' => 'hotel-badge-green',
                                'fallido' => 'hotel-badge-red',
                                'reembolsado' => 'hotel-badge-blue',
                                default => 'hotel-badge-gray',
                            };
                        @endphp
                        <span class="{{ $estadoClass }}">{{ $pago->estado_pago }}</span>
                    </td>
                    <td>
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('pagos.edit', $pago->id) }}" class="hotel-btn-secondary" title="Editar pago">
                                <i data-lucide="pencil"></i>
                                Editar
                            </a>
                            
                            @if($pago->estado_pago === 'pendiente')
                                <form action="{{ route('pagos.procesar', $pago->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="hotel-btn-primary" title="Marcar como completado">
                                        <i data-lucide="check"></i>
                                        Procesar
                                    </button>
                                </form>
                                <form action="{{ route('pagos.cancelar', $pago->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="hotel-btn-secondary text-red-700 border-red-200 hover:bg-red-50" title="Marcar como fallido">
                                        <i data-lucide="x"></i>
                                        Cancelar
                                    </button>
                                </form>
                            @endif

                            @if($pago->estado_pago === 'completado')
                                <form action="{{ route('pagos.reembolsar', $pago->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="hotel-btn-secondary text-blue-700 border-blue-200 hover:bg-blue-50" title="Marcar como reembolsado">
                                        <i data-lucide="rotate-ccw"></i>
                                        Reembolsar
                                    </button>
                                </form>
                            @endif

                            @if($pago->estado_pago !== 'completado')
                                <form action="{{ route('pagos.destroy', $pago->id) }}" method="POST" class="inline"
                                      data-confirm-delete="¿Eliminar este registro de pago #{{ $pago->id }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="hotel-btn-danger" title="Eliminar pago">
                                        <i data-lucide="trash-2"></i>
                                        Eliminar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-gray-500">
                        No hay pagos registrados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
