<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contratos extends Model
{
    use HasFactory;

    protected $fillable = [
        'clienteid',
        'total',
        'total_descuento', // Agrega este campo
        'descuento',
        'requerimiento',
        'ubicacion',
        'departamento',
        'provincia',
        'distrito',
    ];

    public function detalles()
    {
        return $this->morphMany(DetallesCotizacion::class, 'detalleable');
    }

    // Definición de la relación con el modelo Clientes
    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'clienteid');
    }

    public function detallescontrato()
    {
        return $this->belongsTo(DetallesContratos::class, 'contratoid');
    }
}
