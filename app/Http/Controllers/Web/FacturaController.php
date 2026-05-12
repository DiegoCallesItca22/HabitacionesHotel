<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use App\Models\Reserva;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacturaController extends Controller
{
    public function index()
    {
        $facturas = Factura::with(['reserva.usuario', 'pagos'])->orderBy('fecha_emision', 'desc')->get();
        return view('facturas.index', compact('facturas'));
    }

    public function create()
    {
        $reservas = Reserva::where('estado', 'confirmada')->doesntHave('factura')->orderBy('id', 'desc')->get();
        return view('facturas.create', compact('reservas'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'reserva_id' => 'required|exists:reservas,id|unique:facturas,reserva_id',
                'impuestos' => 'nullable|numeric|min:0',
            ]);

            return DB::transaction(function () use ($validatedData) {
                $reserva = Reserva::with(['detalleReservas', 'servicios'])->findOrFail($validatedData['reserva_id']);

                if ($reserva->estado !== 'confirmada') {
                    return redirect()->back()->withInput()->with('error', 'Solo se pueden generar facturas para reservas confirmadas.');
                }

                $subtotal = $reserva->subtotal_habitaciones + $reserva->subtotal_servicios;
                $impuestos = $validatedData['impuestos'] ?? ($subtotal * 0.16);

                $factura = new Factura();
                $factura->reserva_id = $reserva->id;
                $factura->numero_factura = 'FAC-' . date('Y') . '-' . str_pad((string) (Factura::count() + 1), 6, '0', STR_PAD_LEFT);
                $factura->subtotal = $subtotal;
                $factura->impuestos = $impuestos;
                $factura->total = $subtotal + $impuestos;
                $factura->fecha_emision = now();
                $factura->activo = true;

                return $factura->save()
                    ? redirect()->route('facturas.index')->with('success', 'Factura generada exitosamente.')
                    : redirect()->back()->withInput()->with('error', 'Error al generar la factura.');
            });
        } catch (Exception $ex) {
            return redirect()->back()->withInput()->with('error', 'Error grave al generar la factura.');
        }
    }

    public function edit(string $id)
    {
        try {
            $factura = Factura::find($id);

            if ($factura == null) {
                return redirect()->route('facturas.index')->with('error', 'Registro no encontrado.');
            }

            return view('facturas.edit', compact('factura'));
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Error grave al buscar el registro.');
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $factura = Factura::find($id);

            if ($factura == null) {
                return redirect()->route('facturas.index')->with('error', 'Registro no encontrado.');
            }

            $validatedData = $request->validate([
                'activo' => 'nullable|boolean',
            ]);

            if (!$request->boolean('activo') && $factura->pagos()->where('estado_pago', 'completado')->exists()) {
                return redirect()->back()->with('error', 'No se puede desactivar una factura con pagos completados.');
            }

            $factura->activo = $request->boolean('activo');

            return $factura->save()
                ? redirect()->route('facturas.index')->with('success', 'Registro actualizado exitosamente.')
                : redirect()->back()->withInput()->with('error', 'Error al actualizar el registro.');
        } catch (Exception $ex) {
            return redirect()->back()->withInput()->with('error', 'Error grave al actualizar el registro.');
        }
    }

    public function destroy(string $id)
    {
        try {
            $factura = Factura::find($id);

            if ($factura == null) {
                return redirect()->route('facturas.index')->with('error', 'Registro no encontrado.');
            }

            if ($factura->pagos()->exists()) {
                return redirect()->back()->with('error', 'No se puede eliminar una factura con pagos registrados.');
            }

            return $factura->delete()
                ? redirect()->route('facturas.index')->with('success', 'Registro eliminado exitosamente.')
                : redirect()->back()->with('error', 'Error al eliminar el registro.');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Error grave al eliminar el registro.');
        }
    }
}
