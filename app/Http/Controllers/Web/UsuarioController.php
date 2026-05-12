<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::orderBy('creado_en', 'desc')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:100',
                'correo' => 'required|email|max:150|unique:usuarios,correo',
                'password' => 'required|string|min:8',
                'rol' => ['required', Rule::in(['admin', 'cliente', 'recepcionista'])],
                'activo' => 'nullable|boolean',
            ]);

            $data = new Usuario();
            $data->nombre = $validatedData['nombre'];
            $data->correo = $validatedData['correo'];
            $data->password = Hash::make($validatedData['password']);
            $data->rol = $validatedData['rol'];
            $data->activo = $request->boolean('activo', true);

            return $data->save()
                ? redirect()->route('usuarios.index')->with('success', 'Registro creado exitosamente.')
                : redirect()->back()->withInput()->with('error', 'Error al crear el registro.');
        } catch (Exception $ex) {
            return redirect()->back()->withInput()->with('error', 'Error grave al crear el registro.');
        }
    }

    public function edit(string $id)
    {
        try {
            $usuario = Usuario::find($id);

            if ($usuario == null) {
                return redirect()->route('usuarios.index')->with('error', 'Registro no encontrado.');
            }

            return view('usuarios.edit', compact('usuario'));
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Error grave al buscar el registro.');
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $usuario = Usuario::find($id);

            if ($usuario == null) {
                return redirect()->route('usuarios.index')->with('error', 'Registro no encontrado.');
            }

            $validatedData = $request->validate([
                'nombre' => 'required|string|max:100',
                'correo' => ['required', 'email', 'max:150', Rule::unique('usuarios', 'correo')->ignore($usuario->id)],
                'password' => 'nullable|string|min:8',
                'rol' => ['required', Rule::in(['admin', 'cliente', 'recepcionista'])],
                'activo' => 'nullable|boolean',
            ]);

            $usuario->nombre = $validatedData['nombre'];
            $usuario->correo = $validatedData['correo'];
            $usuario->rol = $validatedData['rol'];
            $usuario->activo = $request->boolean('activo');

            if (!empty($validatedData['password'])) {
                $usuario->password = Hash::make($validatedData['password']);
            }

            return $usuario->save()
                ? redirect()->route('usuarios.index')->with('success', 'Registro actualizado exitosamente.')
                : redirect()->back()->withInput()->with('error', 'Error al actualizar el registro.');
        } catch (Exception $ex) {
            return redirect()->back()->withInput()->with('error', 'Error grave al actualizar el registro.');
        }
    }

    public function destroy(string $id)
    {
        try {
            $usuario = Usuario::find($id);

            if ($usuario == null) {
                return redirect()->route('usuarios.index')->with('error', 'Registro no encontrado.');
            }

            if ($usuario->reservas()->where('estado', 'confirmada')->exists()) {
                return redirect()->back()->with('error', 'No se puede eliminar un usuario con reservas confirmadas.');
            }

            return $usuario->delete()
                ? redirect()->route('usuarios.index')->with('success', 'Registro eliminado exitosamente.')
                : redirect()->back()->with('error', 'Error al eliminar el registro.');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Error grave al eliminar el registro.');
        }
    }
}
