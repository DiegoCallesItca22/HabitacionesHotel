@extends('layouts.app')

@section('title', 'Métricas')

@section('content')
<h1>Métricas del Hotel</h1>
<p style="color:#666;">Panel de análisis — solo visible para administradores</p>

{{-- TARJETAS RESUMEN --}}
<table cellpadding="12" cellspacing="0" style="width:100%; margin-bottom:24px;">
    <tr>
        <td style="background:#e8f4fd; border:1px solid #c9e2f5; text-align:center; width:25%;">
            <div style="font-size:2em; font-weight:bold;">{{ $totalReservas }}</div>
            <div>Total reservas</div>
            <small>✅ {{ $reservasConfirmadas }} | ⏳ {{ $reservasPendientes }} | ❌ {{ $reservasCanceladas }}</small>
        </td>
        <td style="background:#e8fde8; border:1px solid #c9f5c9; text-align:center; width:25%;">
            <div style="font-size:2em; font-weight:bold;">{{ $tasaOcupacion }}%</div>
            <div>Tasa de ocupación</div>
            <small>{{ $habitacionesOcupadas }} / {{ $totalHabitaciones }} habitaciones</small>
        </td>
        <td style="background:#fdf8e8; border:1px solid #f5ecc9; text-align:center; width:25%;">
            <div style="font-size:2em; font-weight:bold;">{{ $totalClientes }}</div>
            <div>Clientes activos</div>
        </td>
        <td style="background:#fde8e8; border:1px solid #f5c9c9; text-align:center; width:25%;">
            <div style="font-size:2em; font-weight:bold;">${{ number_format($ingresosRecaudados, 2) }}</div>
            <div>Ingresos recaudados</div>
            <small>Facturado: ${{ number_format($ingresosTotales, 2) }} | Pendiente: ${{ number_format($saldoPendiente, 2) }}</small>
        </td>
    </tr>
</table>

{{-- FILA 1 --}}
<table cellpadding="0" cellspacing="16" style="width:100%; margin-bottom:16px;">
    <tr>
        <td style="width:48%; vertical-align:top;">
            <h3>Reservas por estado</h3>
            <table border="1" cellpadding="8" cellspacing="0" style="width:100%;">
                <thead><tr style="background:#f0f0f0;"><th>Estado</th><th>Cantidad</th><th>%</th></tr></thead>
                <tbody>
                    @foreach($reservasPorEstado as $estado => $cantidad)
                    <tr>
                        <td>{{ $estado }}</td>
                        <td>{{ $cantidad }}</td>
                        <td>{{ $totalReservas > 0 ? round(($cantidad / $totalReservas) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div id="chart_reservas_estado" style="margin-top:12px; height:200px; background:#f9f9f9; border:1px dashed #ccc; display:flex; align-items:center; justify-content:center; color:#999;">
                [ Gráfico de dona — Chart.js ]
            </div>
        </td>
        <td style="width:48%; vertical-align:top;">
            <h3>Ingresos últimos 6 meses</h3>
            <table border="1" cellpadding="8" cellspacing="0" style="width:100%;">
                <thead><tr style="background:#f0f0f0;"><th>Mes</th><th>Total ($)</th></tr></thead>
                <tbody>
                    @forelse($ingresosPorMes as $mes => $total)
                    <tr><td>{{ $mes }}</td><td>${{ number_format($total, 2) }}</td></tr>
                    @empty
                    <tr><td colspan="2">Sin ingresos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div id="chart_ingresos_mes" style="margin-top:12px; height:200px; background:#f9f9f9; border:1px dashed #ccc; display:flex; align-items:center; justify-content:center; color:#999;">
                [ Gráfico de barras — Chart.js ]
            </div>
        </td>
    </tr>
</table>

{{-- FILA 2 --}}
<table cellpadding="0" cellspacing="16" style="width:100%; margin-bottom:16px;">
    <tr>
        <td style="width:48%; vertical-align:top;">
            <h3>Habitaciones más reservadas (por tipo)</h3>
            <table border="1" cellpadding="8" cellspacing="0" style="width:100%;">
                <thead><tr style="background:#f0f0f0;"><th>Tipo</th><th>Reservas</th></tr></thead>
                <tbody>
                    @forelse($reservasPorTipo as $row)
                    <tr><td>{{ ucfirst($row->tipo) }}</td><td>{{ $row->total }}</td></tr>
                    @empty
                    <tr><td colspan="2">Sin datos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </td>
        <td style="width:48%; vertical-align:top;">
            <h3>Servicios más solicitados</h3>
            <table border="1" cellpadding="8" cellspacing="0" style="width:100%;">
                <thead><tr style="background:#f0f0f0;"><th>#</th><th>Servicio</th><th>Unidades</th></tr></thead>
                <tbody>
                    @forelse($serviciosMasSolicitados as $i => $row)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $row->nombre }}</td><td>{{ $row->total_solicitado }}</td></tr>
                    @empty
                    <tr><td colspan="3">Sin datos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </td>
    </tr>
</table>

{{-- TOP CLIENTES --}}
<h3>Top 5 clientes con más reservas</h3>
<table border="1" cellpadding="8" cellspacing="0" style="width:50%;">
    <thead><tr style="background:#f0f0f0;"><th>#</th><th>Cliente</th><th>Reservas</th></tr></thead>
    <tbody>
        @forelse($topClientes as $i => $row)
        <tr><td>{{ $i + 1 }}</td><td>{{ $row->nombre }}</td><td>{{ $row->total_reservas }}</td></tr>
        @empty
        <tr><td colspan="3">Sin datos.</td></tr>
        @endforelse
    </tbody>
</table>

<script>
    // Datos listos para Chart.js (para el equipo frontend):
    const datosReservasPorEstado = @json($reservasPorEstado);
    const datosIngresosPorMes   = @json($ingresosPorMes);
    const datosReservasPorTipo  = {
        labels: @json($reservasPorTipo->pluck('tipo')),
        data:   @json($reservasPorTipo->pluck('total')),
    };
    const datosServicios = {
        labels: @json($serviciosMasSolicitados->pluck('nombre')),
        data:   @json($serviciosMasSolicitados->pluck('total_solicitado')),
    };
</script>
@endsection