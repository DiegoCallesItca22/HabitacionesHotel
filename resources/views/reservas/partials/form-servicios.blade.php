@php
    $serviciosJson = $servicios->map(fn ($s) => [
        'id' => $s->id,
        'nombre' => $s->nombre,
        'precio' => (float) $s->precio,
    ])->values();
    $habitacionesJson = $habitaciones->map(fn ($h) => [
        'id' => $h->id,
        'numero' => $h->numero,
        'precio' => (float) $h->precio_por_noche,
    ])->values();
    $serviciosIniciales = $serviciosReserva ?? [];
@endphp

<p>Servicio:
    <select id="servicio-select">
        <option value="">Sin servicio</option>
        @foreach($servicios as $servicio)
            <option value="{{ $servicio->id }}" data-precio="{{ $servicio->precio }}">{{ $servicio->nombre }} — ${{ number_format((float) $servicio->precio, 2) }}</option>
        @endforeach
    </select>
    Cantidad: <input type="number" id="servicio-cantidad" min="1" value="1">
    <button type="button" id="btn-agregar-servicio">Agregar servicio</button>
</p>

<div id="servicios-tabla-wrap" style="display:none; margin:12px 0;">
    <table border="1" cellpadding="6" cellspacing="0" id="servicios-tabla">
        <thead>
            <tr><th>Servicio</th><th>Precio unit. (USD)</th><th>Cantidad</th><th>Subtotal</th><th></th></tr>
        </thead>
        <tbody id="servicios-tbody"></tbody>
    </table>
</div>

<p><strong>Noches:</strong> <span id="prev-noches">0</span></p>
<p><strong>Total estimado (USD):</strong> $<span id="prev-total">0.00</span></p>

<script>
(function () {
    const catalogo = @json($serviciosJson);
    const habitaciones = @json($habitacionesJson);
    let lineas = @json($serviciosIniciales);

    const selectServicio = document.getElementById('servicio-select');
    const inputCantidad = document.getElementById('servicio-cantidad');
    const btnAgregar = document.getElementById('btn-agregar-servicio');
    const wrapTabla = document.getElementById('servicios-tabla-wrap');
    const tbody = document.getElementById('servicios-tbody');
    const habitacionSelect = document.querySelector('[name="habitacion_id"]');
    const fechaEntrada = document.querySelector('[name="fecha_entrada"]');
    const fechaSalida = document.querySelector('[name="fecha_salida"]');
    const form = habitacionSelect ? habitacionSelect.closest('form') : null;

    function nombreServicio(id) {
        const s = catalogo.find(x => x.id === id);
        return s ? s.nombre : 'Servicio';
    }

    function precioServicio(id) {
        const s = catalogo.find(x => x.id === id);
        return s ? s.precio : 0;
    }

    function calcularNoches() {
        if (!fechaEntrada || !fechaSalida || !fechaEntrada.value || !fechaSalida.value) return 0;
        const entrada = new Date(fechaEntrada.value + 'T00:00:00');
        const salida = new Date(fechaSalida.value + 'T00:00:00');
        const diff = Math.round((salida - entrada) / (1000 * 60 * 60 * 24));
        return diff > 0 ? diff : 0;
    }

    function precioHabitacion() {
        if (!habitacionSelect) return 0;
        const h = habitaciones.find(x => x.id === parseInt(habitacionSelect.value, 10));
        return h ? h.precio : 0;
    }

    function actualizarTotales() {
        const noches = calcularNoches();
        const subHabitacion = noches * precioHabitacion();
        const subServicios = lineas.reduce((sum, l) => sum + (l.cantidad * l.precio), 0);
        document.getElementById('prev-noches').textContent = noches;
        document.getElementById('prev-total').textContent = (subHabitacion + subServicios).toFixed(2);
    }

    function renderTabla() {
        tbody.innerHTML = '';
        lineas.forEach((linea, index) => {
            const sub = linea.cantidad * linea.precio;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${nombreServicio(linea.servicio_id)}</td>
                <td>$${linea.precio.toFixed(2)}</td>
                <td><input type="number" min="1" value="${linea.cantidad}" data-index="${index}" class="cantidad-linea"></td>
                <td>$${sub.toFixed(2)}</td>
                <td><button type="button" data-index="${index}" class="btn-quitar">Quitar</button></td>
            `;
            tbody.appendChild(tr);
        });

        wrapTabla.style.display = lineas.length ? 'block' : 'none';
        actualizarTotales();
        sincronizarHidden();
    }

    function sincronizarHidden() {
        if (!form) return;
        form.querySelectorAll('input[data-servicio-hidden]').forEach(el => el.remove());
        lineas.forEach((linea, i) => {
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = `servicios[${i}][servicio_id]`;
            idInput.value = linea.servicio_id;
            idInput.setAttribute('data-servicio-hidden', '1');
            form.appendChild(idInput);

            const cantInput = document.createElement('input');
            cantInput.type = 'hidden';
            cantInput.name = `servicios[${i}][cantidad]`;
            cantInput.value = linea.cantidad;
            cantInput.setAttribute('data-servicio-hidden', '1');
            form.appendChild(cantInput);
        });
    }

    btnAgregar.addEventListener('click', function () {
        const servicioId = parseInt(selectServicio.value, 10);
        const cantidad = parseInt(inputCantidad.value, 10) || 0;
        if (!servicioId || cantidad < 1) return;

        const existente = lineas.find(l => l.servicio_id === servicioId);
        if (existente) {
            existente.cantidad += cantidad;
        } else {
            lineas.push({
                servicio_id: servicioId,
                precio: precioServicio(servicioId),
                cantidad: cantidad,
            });
        }

        selectServicio.value = '';
        inputCantidad.value = 1;
        renderTabla();
    });

    tbody.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-quitar')) {
            lineas.splice(parseInt(e.target.dataset.index, 10), 1);
            renderTabla();
        }
    });

    tbody.addEventListener('change', function (e) {
        if (e.target.classList.contains('cantidad-linea')) {
            const idx = parseInt(e.target.dataset.index, 10);
            lineas[idx].cantidad = Math.max(1, parseInt(e.target.value, 10) || 1);
            renderTabla();
        }
    });

    [habitacionSelect, fechaEntrada, fechaSalida].forEach(el => {
        if (el) el.addEventListener('change', actualizarTotales);
    });

    renderTabla();
})();
</script>
