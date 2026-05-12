<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use Exception;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function index()
    {
        $servicios = Servicio::orderBy('nombre')->get();
        return view('servicios.index', compact('servicios'));
    }

    public function create()
    {
        return view('servicios.create');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:100',
                'precio' => 'required|numeric|min:0',
                'activo' => 'nullable|boolean',
            ]);

            $data = new Servicio();
            $data->nombre = $validatedData['nombre'];
            $data->precio = $validatedData['precio'];
            $data->activo = $request->boolean('activo', true);

            return $data->save()
                ? redirect()->route('servicios.index')->with('success', 'Registro creado exitosamente.')
                : redirect()->back()->withInput()->with('error', 'Error al crear el registro.');
        } catch (Exception $ex) {
            return redirect()->back()->withInput()->with('error', 'Error grave al crear el registro.');
        }
    }

    public function edit(string $id)
    {
        try {
            $servicio = Servicio::find($id);

            if ($servicio == null) {
                return redirect()->route('servicios.index')->with('error', 'Registro no encontrado.');
            }

            return view('servicios.edit', compact('servicio'));
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Error grave al buscar el registro.');
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $servicio = Servicio::find($id);

            if ($servicio == null) {
                return redirect()->route('servicios.index')->with('error', 'Registro no encontrado.');
            }

            $validatedData = $request->validate([
                'nombre' => 'required|string|max:100',
                'precio' => 'required|numeric|min:0',
                'activo' => 'nullable|boolean',
            ]);

            $servicio->nombre = $validatedData['nombre'];
            $servicio->precio = $validatedData['precio'];
            $servicio->activo = $request->boolean('activo');

            return $servicio->save()
                ? redirect()->route('servicios.index')->with('success', 'Registro actualizado exitosamente.')
                : redirect()->back()->withInput()->with('error', 'Error al actualizar el registro.');
        } catch (Exception $ex) {
            return redirect()->back()->withInput()->with('error', 'Error grave al actualizar el registro.');
        }
    }

    public function destroy(string $id)
    {
        try {
            $servicio = Servicio::find($id);

            if ($servicio == null) {
                return redirect()->route('servicios.index')->with('error', 'Registro no encontrado.');
            }

            if ($servicio->reservaServicios()->exists()) {
                return redirect()->back()->with('error', 'No se puede eliminar un servicio asociado a reservas.');
            }

            return $servicio->delete()
                ? redirect()->route('servicios.index')->with('success', 'Registro eliminado exitosamente.')
                : redirect()->back()->with('error', 'Error al eliminar el registro.');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Error grave al eliminar el registro.');
        }
    }
}
