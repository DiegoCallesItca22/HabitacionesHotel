@extends('layouts.app')

@section('title', 'Métricas')

@section('content')
<h1>Métricas del Hotel</h1>
<p style="color:#666;">Panel de análisis - solo visible para administradores</p>

<table cellpadding="12" cellspacing="0" style="width:100%; margin-bottom:24px;">
    <tr>
        <td style="background:#e8f4fd; border:1px solid #c9e2f5; text-align:center; width:25%;">
            <div style="font-size:2em; font-weight:bold;">{{ $totalReservas }}</div>
            <div>Total reservas</div>
            <small>{{ $reservasConfirmadas }} confirmadas | {{ $reservasPendientes }} pendientes | {{ $reservasCanceladas }} canceladas</small>
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
            <div style="font-size:2em; font-weight:bold;">${{ number_format((float) $ingresosRecaudados, 2) }}</div>
            <div>Ingresos recaudados</div>
            <small>Facturado: ${{ number_format((float) $ingresosTotales, 2) }} | Pendiente: ${{ number_format((float) $saldoPendiente, 2) }}</small>
        </td>
    </tr>
</table>

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
            <canvas id="chart_reservas_estado" width="480" height="220" style="margin-top:12px; width:100%; max-height:220px; border:1px solid #ddd;"></canvas>
        </td>
        <td style="width:48%; vertical-align:top;">
            <h3>Ingresos últimos 6 meses</h3>
            <table border="1" cellpadding="8" cellspacing="0" style="width:100%;">
                <thead><tr style="background:#f0f0f0;"><th>Mes</th><th>Total ($)</th></tr></thead>
                <tbody>
                    @forelse($ingresosPorMes as $mes => $total)
                    <tr><td>{{ $mes }}</td><td>${{ number_format((float) $total, 2) }}</td></tr>
                    @empty
                    <tr><td colspan="2">Sin ingresos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <canvas id="chart_ingresos_mes" width="480" height="220" style="margin-top:12px; width:100%; max-height:220px; border:1px solid #ddd;"></canvas>
        </td>
    </tr>
</table>

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
    const datosReservasPorEstado = @json($reservasPorEstado);
    const datosIngresosPorMes = @json($ingresosPorMes);

    function prepararCanvas(canvas) {
        const ratio = window.devicePixelRatio || 1;
        const width = canvas.clientWidth || canvas.width;
        const height = 220;
        canvas.width = width * ratio;
        canvas.height = height * ratio;
        const ctx = canvas.getContext('2d');
        ctx.scale(ratio, ratio);
        return { ctx, width, height };
    }

    function dibujarMensaje(ctx, width, height, texto) {
        ctx.clearRect(0, 0, width, height);
        ctx.fillStyle = '#777';
        ctx.font = '14px Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(texto, width / 2, height / 2);
    }

    function dibujarDona(id, datos) {
        const canvas = document.getElementById(id);
        const { ctx, width, height } = prepararCanvas(canvas);
        const labels = Object.keys(datos);
        const values = Object.values(datos).map(Number);
        const total = values.reduce((sum, value) => sum + value, 0);
        const colores = ['#2f80ed', '#f2c94c', '#eb5757', '#27ae60'];

        if (!total) {
            dibujarMensaje(ctx, width, height, 'Sin datos para graficar');
            return;
        }

        let inicio = -Math.PI / 2;
        const cx = Math.min(width * 0.36, 170);
        const cy = height / 2;
        const radio = 72;

        values.forEach((value, index) => {
            const angulo = (value / total) * Math.PI * 2;
            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.arc(cx, cy, radio, inicio, inicio + angulo);
            ctx.closePath();
            ctx.fillStyle = colores[index % colores.length];
            ctx.fill();
            inicio += angulo;
        });

        ctx.beginPath();
        ctx.arc(cx, cy, 38, 0, Math.PI * 2);
        ctx.fillStyle = '#fff';
        ctx.fill();
        ctx.fillStyle = '#333';
        ctx.font = 'bold 18px Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(total, cx, cy + 6);

        labels.forEach((label, index) => {
            const x = Math.min(width * 0.62, cx + 120);
            const y = 64 + index * 34;
            ctx.fillStyle = colores[index % colores.length];
            ctx.fillRect(x, y - 12, 16, 16);
            ctx.fillStyle = '#333';
            ctx.font = '13px Arial, sans-serif';
            ctx.textAlign = 'left';
            ctx.fillText(`${label}: ${values[index]}`, x + 24, y + 1);
        });
    }

    function dibujarBarras(id, datos) {
        const canvas = document.getElementById(id);
        const { ctx, width, height } = prepararCanvas(canvas);
        const labels = Object.keys(datos);
        const values = Object.values(datos).map(Number);
        const max = Math.max(...values, 0);

        if (!max) {
            dibujarMensaje(ctx, width, height, 'Sin ingresos para graficar');
            return;
        }

        const margen = { top: 24, right: 16, bottom: 42, left: 52 };
        const graphWidth = width - margen.left - margen.right;
        const graphHeight = height - margen.top - margen.bottom;
        const barWidth = Math.max(24, graphWidth / labels.length - 18);

        ctx.strokeStyle = '#ccc';
        ctx.beginPath();
        ctx.moveTo(margen.left, margen.top);
        ctx.lineTo(margen.left, margen.top + graphHeight);
        ctx.lineTo(width - margen.right, margen.top + graphHeight);
        ctx.stroke();

        labels.forEach((label, index) => {
            const barHeight = (values[index] / max) * graphHeight;
            const x = margen.left + index * (graphWidth / labels.length) + 9;
            const y = margen.top + graphHeight - barHeight;

            ctx.fillStyle = '#27ae60';
            ctx.fillRect(x, y, barWidth, barHeight);
            ctx.fillStyle = '#333';
            ctx.font = '12px Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText(label, x + barWidth / 2, height - 16);
            ctx.fillText(`$${values[index].toFixed(2)}`, x + barWidth / 2, Math.max(16, y - 6));
        });
    }

    dibujarDona('chart_reservas_estado', datosReservasPorEstado);
    dibujarBarras('chart_ingresos_mes', datosIngresosPorMes);
</script>
@endsection
