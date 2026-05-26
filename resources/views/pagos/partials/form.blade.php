<div class="space-y-4">
    <div>
        <label class="hotel-label" for="factura_id">Factura Relacionada</label>
        <select name="factura_id" id="factura_id" class="hotel-select bg-gray-50 disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed" required @isset($pago) disabled @endisset>
            <option value="">-- Seleccione una factura --</option>
            @foreach($facturas as $factura)
                <option value="{{ $factura->id }}" @selected(isset($pago) && $pago->factura_id == $factura->id)>
                    {{ $factura->numero_factura }} — Saldo Pendiente: ${{ number_format((float) $factura->saldo_pendiente, 2) }}
                </option>
            @endforeach
        </select>
        @isset($pago)
            <input type="hidden" name="factura_id" value="{{ $pago->factura_id }}">
        @endisset
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label class="hotel-label" for="monto">Monto del Pago (USD)</label>
            <input type="number" step="0.01" min="0.01" name="monto" id="monto" value="{{ old('monto', $pago->monto ?? '') }}" class="hotel-input font-mono" required placeholder="0.00">
        </div>

        <div>
            <label class="hotel-label" for="metodo_pago">Método de Pago</label>
            <select name="metodo_pago" id="metodo_pago" class="hotel-select" required>
                @foreach(['efectivo' => 'Efectivo', 'tarjeta_credito' => 'Tarjeta crédito', 'tarjeta_debito' => 'Tarjeta débito', 'transferencia' => 'Transferencia'] as $key => $label)
                    <option value="{{ $key }}" @selected(old('metodo_pago', $pago->metodo_pago ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="hotel-label" for="estado_pago">Estado del Pago</label>
            <select name="estado_pago" id="estado_pago" class="hotel-select" required>
                @foreach(['pendiente' => 'Pendiente', 'completado' => 'Completado', 'fallido' => 'Fallido', 'reembolsado' => 'Reembolsado'] as $estadoKey => $estadoLabel)
                    <option value="{{ $estadoKey }}" @selected(old('estado_pago', $pago->estado_pago ?? 'pendiente') === $estadoKey)>{{ $estadoLabel }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>