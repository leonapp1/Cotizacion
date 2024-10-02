<?php

namespace App\Http\Controllers;

use App\Models\Cotizaciones;
use App\Models\Productos;
use App\Models\Clientes;
use App\Models\Contratos;
use App\Models\DetallesCotizacion;
use App\Models\Observaciones;
use Illuminate\Http\Request;
use Inertia\Inertia;



class DetallesCotizacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cotizaciones = Cotizaciones::with('cliente')->get();
        $productos = Productos::all();
        $clientes = Clientes::all();

        return inertia::render('DetallesCotizacion/Index', [
            'cotizaciones' => $cotizaciones,
            'productos' => $productos,
            'clientes' => $clientes,
            'valor' => "0",
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $contratos = Contratos::all();
        $Productos = Productos::all();
        $clientes = Clientes::all();
        $observaciones = Observaciones::all();
        $cotizaciones = Cotizaciones::with('detallesCotizacion','obsevaciones')->findOrFail($id);

        return inertia::render(
            'Contratos/Show',
            [
                'contratos' => $contratos,
                'productos' => $Productos,
                "clientes" => $clientes,
                "observaciones" => $observaciones,
                'valor' => "0",
                'cotizaciones'=>$cotizaciones,

            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DetallesCotizacion $detallesCotizacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DetallesCotizacion $detallesCotizacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DetallesCotizacion $detallesCotizacion)
    {
        //
    }
    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'clienteid');
    }
}
