<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallesContratos extends Model
{
    use HasFactory;
    public function producto()
    {
        return $this->belongsTo(Productos::class, 'productoid');
    }

    public function cotizacion()
    {
        return $this->belongsTo(Contratos::class, 'contratoid');
    }
    public function contratos()
    {
        return $this->belongsTo(Contratos::class, 'contratoid');
    }

}
