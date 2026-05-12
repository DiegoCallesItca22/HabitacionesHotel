<p>Factura:
    <select name="factura_id" required @isset($pago) disabled @endisset>
        @foreach($facturas as $factura)
            <option value="{{ $factura->id }}" @selected(isset($pago) && $pago->factura_id == $factura->id)>{{ $factura->numero_factura }} - saldo {{ number_format((float) $factura->saldo_pendiente, 2) }}</option>
        @endforeach
    </select>
</p>
<p>Monto: <input type="number" step="0.01" name="monto" value="{{ old('monto', $pago->monto ?? '') }}" required></p>
<p>Metodo:
    <select name="metodo_pago" required>
        @foreach(['efectivo' => 'Efectivo', 'tarjeta_credito' => 'Tarjeta credito', 'tarjeta_debito' => 'Tarjeta debito', 'transferencia' => 'Transferencia'] as $key => $label)
            <option value="{{ $key }}" @selected(old('metodo_pago', $pago->metodo_pago ?? '') === $key)>{{ $label }}</option>
        @endforeach
    </select>
</p>
<p>Estado:
    <select name="estado_pago" required>
        @foreach(['pendiente', 'completado', 'fallido', 'reembolsado'] as $estado)
            <option value="{{ $estado }}" @selected(old('estado_pago', $pago->estado_pago ?? 'pendiente') === $estado)>{{ $estado }}</option>
        @endforeach
    </select>
</p>
<p><label><input type="checkbox" name="activo" value="1" @checked(old('activo', $pago->activo ?? true))> Activo</label></p>
