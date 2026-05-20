<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DetalleReserva;
use App\Models\Habitacion;
use App\Models\Reserva;
use App\Models\ReservaServicio;
use App\Models\Servicio;
use App\Models\Usuario;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservaController extends Controller
{
    public function index()
    {
        $reservas = Reserva::with(['usuario', 'detalleReservas.habitacion', 'servicios', 'factura'])
            ->orderBy('creado_en', 'desc')->get();
        return view('reservas.index', compact('reservas'));
    }

    public function create()
    {
        $usuarios    = Usuario::where('rol', 'cliente')->where('activo', true)->orderBy('nombre')->get();
        $habitaciones = Habitacion::where('activo', true)->orderBy('numero')->get();
        $servicios   = Servicio::where('activo', true)->orderBy('nombre')->get();
        return view('reservas.create', compact('usuarios', 'habitaciones', 'servicios'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'usuario_id'    => 'required|exists:usuarios,id',
            'fecha_entrada' => 'required|date',
            'fecha_salida'  => 'required|date|after:fecha_entrada',
            'habitacion_id' => 'required|exists:habitaciones,id',
            'servicio_id'   => 'nullable|exists:servicios,id',
            'cantidad'      => 'nullable|integer|min:0',
        ]);

        try {
            return DB::transaction(function () use ($validatedData) {
                $habitacion = Habitacion::findOrFail($validatedData['habitacion_id']);

                if (!$habitacion->estaDisponible()) {
                    return redirect()->back()->withInput()->with('error', 'La habitacion no esta disponible.');
                }

                if ($this->habitacionOcupada($habitacion->id, $validatedData['fecha_entrada'], $validatedData['fecha_salida'])) {
                    return redirect()->back()->withInput()->with('error', 'La habitacion esta ocupada en las fechas solicitadas.');
                }

                $reserva = new Reserva();
                $reserva->usuario_id    = $validatedData['usuario_id'];
                $reserva->fecha_entrada = $validatedData['fecha_entrada'];
                $reserva->fecha_salida  = $validatedData['fecha_salida'];
                $reserva->estado        = 'pendiente';
                $reserva->activo        = true;
                $reserva->save();

                $noches = Carbon::parse($validatedData['fecha_entrada'])->diffInDays(Carbon::parse($validatedData['fecha_salida']));

                DetalleReserva::create([
                    'reserva_id'    => $reserva->id,
                    'habitacion_id' => $habitacion->id,
                    'noches'        => $noches,
                    'precio_noche'  => $habitacion->precio_por_noche,
                    'subtotal'      => $noches * $habitacion->precio_por_noche,
                    'activo'        => true,
                ]);

                if (!empty($validatedData['servicio_id'])) {
                    $servicio = Servicio::findOrFail($validatedData['servicio_id']);
                    $cantidad = $validatedData['cantidad'] ?? 1;
                    if ($cantidad > 0) {
                        ReservaServicio::create([
                            'reserva_id'      => $reserva->id,
                            'servicio_id'     => $servicio->id,
                            'cantidad'        => $cantidad,
                            'precio_unitario' => $servicio->precio,
                            'subtotal'        => $cantidad * $servicio->precio,
                            'activo'          => true,
                        ]);
                    }
                }

                return redirect()->route('reservas.index')->with('success', 'Reserva creada exitosamente.');
            });
        } catch (Exception $ex) {
            return redirect()->back()->withInput()->with('error', 'Error al crear la reserva: ' . $ex->getMessage());
        }
    }

    public function edit(string $id)
    {
        try {
            $reserva  = Reserva::find($id);
            $usuarios = Usuario::where('rol', 'cliente')->where('activo', true)->orderBy('nombre')->get();
            if ($reserva == null) {
                return redirect()->route('reservas.index')->with('error', 'Registro no encontrado.');
            }
            return view('reservas.edit', compact('reserva', 'usuarios'));
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Error grave al buscar el registro.');
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $reserva = Reserva::find($id);
            if ($reserva == null) {
                return redirect()->route('reservas.index')->with('error', 'Registro no encontrado.');
            }
            $validatedData = $request->validate([
                'usuario_id'    => 'required|exists:usuarios,id',
                'fecha_entrada' => 'required|date',
                'fecha_salida'  => 'required|date|after:fecha_entrada',
                'estado'        => 'required|in:pendiente,confirmada,cancelada,completada',
                'activo'        => 'nullable|boolean',
            ]);
            $reserva->usuario_id    = $validatedData['usuario_id'];
            $reserva->fecha_entrada = $validatedData['fecha_entrada'];
            $reserva->fecha_salida  = $validatedData['fecha_salida'];
            $reserva->estado        = $validatedData['estado'];
            $reserva->activo        = $request->boolean('activo');
            return $reserva->save()
                ? redirect()->route('reservas.index')->with('success', 'Registro actualizado exitosamente.')
                : redirect()->back()->withInput()->with('error', 'Error al actualizar el registro.');
        } catch (Exception $ex) {
            return redirect()->back()->withInput()->with('error', 'Error grave al actualizar el registro.');
        }
    }

    public function destroy(string $id)
    {
        try {
            $reserva = Reserva::with('factura.pagos')->find($id);
            if ($reserva == null) {
                return redirect()->route('reservas.index')->with('error', 'Registro no encontrado.');
            }
            DB::transaction(function () use ($reserva) {
                $reserva->detalleReservas()->delete();
                $reserva->servicios()->detach();
                if ($reserva->factura) {
                    $reserva->factura->pagos()->delete();
                    $reserva->factura()->delete();
                }
                $reserva->delete();
            });
            return redirect()->route('reservas.index')->with('success', 'Registro eliminado exitosamente.');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Error grave al eliminar el registro.');
        }
    }

    public function confirmar(string $id)
    {
        try {
            $reserva = Reserva::with('detalleReservas.habitacion')->find($id);
            if ($reserva == null || $reserva->estado !== 'pendiente') {
                return redirect()->back()->with('error', 'Solo se pueden confirmar reservas pendientes.');
            }
            DB::transaction(function () use ($reserva) {
                $reserva->update(['estado' => 'confirmada']);
                foreach ($reserva->detalleReservas as $detalle) {
                    $detalle->habitacion->update(['estado' => 'ocupada']);
                }
            });
            return redirect()->route('reservas.index')->with('success', 'Reserva confirmada exitosamente.');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Error grave al confirmar la reserva.');
        }
    }

    public function cancelar(string $id)
    {
        try {
            $reserva = Reserva::with('detalleReservas.habitacion')->find($id);
            if ($reserva == null || $reserva->estado === 'completada') {
                return redirect()->back()->with('error', 'No se puede cancelar una reserva completada.');
            }
            DB::transaction(function () use ($reserva) {
                $estabaConfirmada = $reserva->estado === 'confirmada';
                $reserva->update(['estado' => 'cancelada']);
                if ($estabaConfirmada) {
                    foreach ($reserva->detalleReservas as $detalle) {
                        $detalle->habitacion->update(['estado' => 'disponible']);
                    }
                }
            });
            return redirect()->route('reservas.index')->with('success', 'Reserva cancelada exitosamente.');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Error grave al cancelar la reserva.');
        }
    }

    private function habitacionOcupada(int $habitacionId, string $fechaEntrada, string $fechaSalida): bool
    {
        return DetalleReserva::where('habitacion_id', $habitacionId)
            ->whereHas('reserva', function ($query) use ($fechaEntrada, $fechaSalida) {
                $query->where('estado', 'confirmada')
                    ->where('fecha_entrada', '<', $fechaSalida)
                    ->where('fecha_salida', '>', $fechaEntrada);
            })->exists();
    }
}