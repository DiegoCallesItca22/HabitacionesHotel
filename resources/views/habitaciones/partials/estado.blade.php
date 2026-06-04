<select name="estado" required>
    @foreach(['disponible' => 'Disponible', 'ocupada' => 'Ocupada', 'mantenimiento' => 'Mantenimiento'] as $key => $label)
        <option value="{{ $key }}" @selected(old('estado', $value ?? 'disponible') === $key)>{{ $label }}</option>
    @endforeach
</select>
