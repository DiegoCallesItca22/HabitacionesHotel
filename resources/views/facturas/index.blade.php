@extends('layouts.app')

@section('title', 'Facturas')

@section('content')
@include('partials.page-header', [
    'pageTitle' => 'Facturas',
    'pageSubtitle' => 'Historial de facturación de reservas, impuestos y estados de cuenta.',
    'pageActions' => '<a href="' . route('facturas.create') . '" class="hotel-btn-primary"><i data-lucide="plus"></i> Nueva factura</a>',
])

<div class="hotel-table-wrap animate-hotel-fade">
    <table class="hotel-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Número</th>
                <th>Reserva</th>
                <th>Cliente</th>
                <th>Subtotal</th>
                <th>Impuestos (13%)</th>
                <th>Total</th>
                <th>Pagado</th>
                <th>Saldo Pendiente</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($facturas as $factura)
                <tr>
                    <td class="font-mono text-gray-500">#{{ $factura->id }}</td>
                    <td class="font-bold text-hotel-dark font-mono">{{ $factura->numero_factura }}</td>
                    <td class="font-semibold text-gray-700 font-mono">#{{ $factura->reserva_id }}</td>
                    <td>{{ $factura->reserva?->usuario?->nombre ?? 'N/A' }}</td>
                    <td class="font-mono">${{ number_format((float) $factura->subtotal, 2) }}</td>
                    <td class="font-mono">${{ number_format((float) $factura->impuestos, 2) }}</td>
                    <td class="font-bold text-hotel-dark font-mono">${{ number_format((float) $factura->total, 2) }}</td>
                    <td class="font-mono text-emerald-700 font-semibold">${{ number_format((float) $factura->total_pagado, 2) }}</td>
                    <td class="font-mono font-semibold {{ $factura->saldo_pendiente > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                        ${{ number_format((float) $factura->saldo_pendiente, 2) }}
                    </td>
                    <td>
                        @if($factura->saldo_pendiente <= 0)
                            <span class="hotel-badge-green">Pagado</span>
                        @elseif($factura->total_pagado > 0)
                            <span class="hotel-badge-amber">Abonado</span>
                        @else
                            <span class="hotel-badge-red">Pendiente</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="py-12 text-center text-gray-500">
                        No hay facturas registradas en este momento.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
