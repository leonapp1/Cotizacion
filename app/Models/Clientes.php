<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    use HasFactory;
    protected $fillable=[ 'dni','nombre', 'apellido', 'email', 'telefono','direccion'];

    public function contratos()
    {
        return $this->hasMany(Contratos::class, 'clienteid');
    }

    public function clientes()
    {
        return $this->hasMany(Clientes::class, 'clienteid');
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizaciones::class, 'clienteid');
    }
}
