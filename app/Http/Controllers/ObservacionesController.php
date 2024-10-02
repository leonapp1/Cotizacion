<?php

namespace App\Http\Controllers;

use App\Models\Observaciones;
use Illuminate\Http\Request;
use Inertia\Inertia;
class ObservacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $observaciones=Observaciones::all();
        return inertia::render('Observaciones/Index', ['observaciones' => $observaciones]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia::render('Observaciones/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "nombre" => "required|max:100",
        ]);
        $observaciones = new Observaciones($request->input());
        $observaciones->save();
        return redirect("observaciones");
    }

    /**
     * Display the specified resource.
     */
    public function show(Observaciones $Observaciones)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Observaciones $observaciones)
    {
        return inertia::render('Observaciones/Edit', ['observaciones' => $observaciones]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Observaciones $observaciones,$id)
    {
        $request->validate([
            "nombre" => "required|max:100",
        ]);

        $observaciones = Observaciones::findOrFail($id);
        $observaciones->update($request->all());
        return redirect('observaciones');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Observaciones $observaciones,$id)
    {
        $observaciones = Observaciones::findOrFail($id);

        $observaciones->delete();
        return redirect('observaciones');
    }
}
