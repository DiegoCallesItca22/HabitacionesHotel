<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use App\Models\Habitacion;
use App\Models\Pago;
use App\Models\Reserva;
use App\Models\Usuario;
use Exception;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $stats = [
                'usuarios' => Usuario::count(),
                'habitaciones' => Habitacion::count(),
                'reservas' => Reserva::count(),
                'facturas' => Factura::count(),
                'pagos' => Pago::where('estado_pago', 'completado')->sum('monto'),
            ];

            return view('home.home', compact('stats'));
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Error grave al acceder a la vista.');
        }
    }
}
