<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'facturas';

    protected $fillable = [
        'reserva_id',
        'numero_factura',
        'subtotal',
        'impuestos',
        'total',
        'fecha_emision',
        'activo'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_emision' => 'datetime',
        'activo' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'factura_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function getTotalPagadoAttribute()
    {
        return $this->pagos()->where('estado_pago', 'completado')->sum('monto');
    }

    public function getSaldoPendienteAttribute()
    {
        return $this->total - $this->total_pagado;
    }

    public function estaPagada()
    {
        return $this->total_pagado >= $this->total;
    }

    public function generarNumeroFactura()
    {
        $this->numero_factura = 'FAC-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
        return $this;
    }
}
