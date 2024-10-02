<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizaciones extends Model
{
    use HasFactory;
    protected $fillable=['total', 'requerimiento', 'ubicacion', 'departamento', 'provincia', 'distrito', 'descuento', 'clienteid'];

    // Definición de la relación con el modelo Clientes
    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'clienteid');
    }

    public function detalles()
    {
        return $this->morphMany(DetallesCotizacion::class, 'detalleable');
    }
    public function detallesCotizacion()
    {
        return $this->hasMany(DetallesCotizacion::class, 'cotizacionid');
    }
    public function obsevaciones()
    {
        return $this->hasMany(Observacionescotizaciones::class, 'cotizacionesid');
    }

}
