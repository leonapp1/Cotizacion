<?php

namespace App\Http\Controllers;

use App\Models\DetallesContratos;
use App\Models\Productos;
use App\Models\Clientes;
use App\Models\Contratos;
use Illuminate\Http\Request;
use Inertia\Inertia;


class DetallesContratosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contratos = Contratos::with('cliente')->get();
        $productos = Productos::all();
        $clientes = Clientes::all();

        return inertia::render('DetallesContratos/Index', [
            'contratos' => $contratos,
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
    public function show(DetallesContratos $detallesContratos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DetallesContratos $detallesContratos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DetallesContratos $detallesContratos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DetallesContratos $detallesContratos)
    {
        //
    }
}
