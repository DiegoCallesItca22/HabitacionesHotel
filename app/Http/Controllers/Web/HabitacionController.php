<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Habitacion;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HabitacionController extends Controller
{
    public function index()
    {
        $habitaciones = Habitacion::where('activo', true)->orderBy('numero')->get();
        return view('habitaciones.index', compact('habitaciones'));
    }

    public function create()
    {
        return view('habitaciones.create');
    }

    public function verificarNumero(Request $request)
    {
        $request->validate([
            'numero' => 'required|string|max:10',
            'except_id' => 'nullable|integer',
        ]);

        $query = Habitacion::where('numero', $request->numero)->where('activo', true);

        if ($request->filled('except_id')) {
            $query->where('id', '!=', $request->except_id);
        }

        return response()->json(['existe' => $query->exists()]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'numero' => 'required|string|max:10|unique:habitaciones,numero',
            'tipo' => ['required', Rule::in(['individual', 'doble', 'suite', 'familiar'])],
            'precio_por_noche' => 'required|numeric|min:0',
            'estado' => ['required', Rule::in(['disponible', 'ocupada', 'mantenimiento'])],
            'activo' => 'nullable|boolean',
        ]);

        try {
            $data = new Habitacion();
            $data->numero = $validatedData['numero'];
            $data->tipo = $validatedData['tipo'];
            $data->precio_por_noche = $validatedData['precio_por_noche'];
            $data->estado = $validatedData['estado'];
            $data->activo = $request->boolean('activo', true);

            return $data->save()
                ? redirect()->route('habitaciones.index')->with('success', 'Registro creado exitosamente.')
                : redirect()->back()->withInput()->with('error', 'Error al crear el registro.');
        } catch (Exception $ex) {
            return redirect()->back()->withInput()->with('error', 'Error al crear el registro: ' . $ex->getMessage());
        }
    }

    public function edit(string $id)
    {
        try {
            $habitacion = Habitacion::find($id);

            if ($habitacion == null) {
                return redirect()->route('habitaciones.index')->with('error', 'Registro no encontrado.');
            }

            return view('habitaciones.edit', compact('habitacion'));
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Error al buscar el registro.');
        }
    }

    public function update(Request $request, string $id)
    {
        $habitacion = Habitacion::find($id);

        if ($habitacion == null) {
            return redirect()->route('habitaciones.index')->with('error', 'Registro no encontrado.');
        }

        $validatedData = $request->validate([
            'numero' => ['required', 'string', 'max:10', Rule::unique('habitaciones', 'numero')->ignore($habitacion->id)],
            'tipo' => ['required', Rule::in(['individual', 'doble', 'suite', 'familiar'])],
            'precio_por_noche' => 'required|numeric|min:0',
            'estado' => ['required', Rule::in(['disponible', 'ocupada', 'mantenimiento'])],
            'activo' => 'nullable|boolean',
        ]);

        try {
            $habitacion->numero = $validatedData['numero'];
            $habitacion->tipo = $validatedData['tipo'];
            $habitacion->precio_por_noche = $validatedData['precio_por_noche'];
            $habitacion->estado = $validatedData['estado'];
            $habitacion->activo = $request->boolean('activo');

            return $habitacion->save()
                ? redirect()->route('habitaciones.index')->with('success', 'Registro actualizado exitosamente.')
                : redirect()->back()->withInput()->with('error', 'Error al actualizar el registro.');
        } catch (Exception $ex) {
            return redirect()->back()->withInput()->with('error', 'Error al actualizar el registro: ' . $ex->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $habitacion = Habitacion::find($id);

            if ($habitacion == null) {
                return redirect()->route('habitaciones.index')->with('error', 'Registro no encontrado.');
            }

            if ($habitacion->detalleReservas()->whereHas('reserva', fn ($query) => $query->where('estado', 'confirmada'))->exists()) {
                return redirect()->back()->with('error', 'No se puede eliminar una habitacion con reservas confirmadas.');
            }

            $habitacion->activo = false;

            return $habitacion->save()
                ? redirect()->route('habitaciones.index')->with('success', 'Registro desactivado exitosamente.')
                : redirect()->back()->with('error', 'Error al desactivar el registro.');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Error al desactivar el registro.');
        }
    }
}
