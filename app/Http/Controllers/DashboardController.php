<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Contratos;
use App\Models\Cotizaciones;
use App\Models\Dashboard;
use App\Models\DetallesContratos;
use App\Models\Productos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Clientes::all();
        $productos = Productos::all();
        $cotizaciones = Cotizaciones::with('detallesCotizacion.producto')->get();
        $contratos = Contratos::with('cliente')->get();
        // Obtener productos más vendidos
        $productosMasVendidos = DetallesContratos::select('productoid', DB::raw('SUM(cantidad) as total_cantidad'))
            ->groupBy('productoid')
            ->orderBy('total_cantidad', 'desc')
            ->with('producto')
            ->take(10) // Limitar a los 10 más vendidos
            ->get();

        return inertia('Dashboard', [
            'clientes' => $clientes,
            'productos' => $productos,
            'cotizaciones' => $cotizaciones,
            'contratos' => $contratos,
            'productosMasVendidos' => $productosMasVendidos,
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
    public function show(Dashboard $dashboard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dashboard $dashboard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dashboard $dashboard)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dashboard $dashboard)
    {
        //
    }
}
