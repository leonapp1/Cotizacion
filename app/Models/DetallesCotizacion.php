<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallesCotizacion extends Model
{
    use HasFactory;
    protected $fillable=[
    "cantidad",
    "precio_unitario",
    'subtotal',
    'Cotizacionid',
    'Productoid'
];
public function detalleable()
    {
        return $this->morphTo();
    }

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'productoid');
    }

    public function cotizaciones()
    {
        return $this->belongsTo(cotizaciones::class, 'cotizacionid');
    }

}
