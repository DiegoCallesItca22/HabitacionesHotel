@extends('layouts.app')

@section('title', 'Crear factura')

@section('content')
<h1>Crear factura</h1>

<p style="background:#f0f8e8; padding:8px; border-left:4px solid green;">
    El IVA se calcula automáticamente al 13% (tasa estándar El Salvador).
</p>

<form action="{{ route('facturas.store') }}" method="POST">
    @csrf
    <p>
        Reserva confirmada:
        <select name="reserva_id" id="reserva_select" required onchange="calcularPreview(this)">
            <option value="">-- Seleccione una reserva --</option>
            @foreach($reservas as $reserva)
                <option value="{{ $reserva->id }}"
                    data-subtotal="{{ $reserva->subtotal_habitaciones + $reserva->subtotal_servicios }}">
                    Reserva #{{ $reserva->id }} — {{ $reserva->usuario->nombre }}
                    ({{ $reserva->fecha_entrada->format('d/m/Y') }} al {{ $reserva->fecha_salida->format('d/m/Y') }})
                    — ${{ number_format($reserva->subtotal_habitaciones + $reserva->subtotal_servicios, 2) }}
                </option>
            @endforeach
        </select>
    </p>

    <div id="preview" style="display:none; background:#f9f9f9; border:1px solid #ccc; padding:10px; max-width:300px;">
        <p><strong>Vista previa</strong></p>
        <p>Subtotal: $<span id="prev_subtotal">0.00</span></p>
        <p>IVA (13%): $<span id="prev_iva">0.00</span></p>
        <p><strong>Total: $<span id="prev_total">0.00</span></strong></p>
    </div>

    <br>
    <button type="submit">Generar factura</button>
    <a href="{{ route('facturas.index') }}">Cancelar</a>
</form>

<script>
function calcularPreview(select) {
    const opt     = select.options[select.selectedIndex];
    const preview = document.getElementById('preview');
    if (!select.value) { preview.style.display = 'none'; return; }
    const subtotal = parseFloat(opt.dataset.subtotal) || 0;
    const iva      = Math.round(subtotal * 0.13 * 100) / 100;
    const total    = subtotal + iva;
    document.getElementById('prev_subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('prev_iva').textContent      = iva.toFixed(2);
    document.getElementById('prev_total').textContent    = total.toFixed(2);
    preview.style.display = 'block';
}
</script>
@endsection