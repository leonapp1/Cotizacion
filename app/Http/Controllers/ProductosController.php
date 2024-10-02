<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productos = Productos::all();
        return inertia::render('Productos/Index', ['productos' => $productos]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia::render('Productos/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "nombre" => "required|max:500",
            "descripcion" => "required",
            "precio" => "required|numeric",
            "img" => "required|image|mimes:jpeg,png,jpg,gif|max:2048",
        ]);
        $productos = new Productos($request->except('img'));

        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $productos->img = 'images/' . $imageName;
        }

        $productos->save();

        return redirect("productos");
    }

    /**
     * Display the specified resource.
     */
    public function show(Productos $productos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Productos $productos)
    {
        return inertia::render('Productos/Edit', ['productos' => $productos]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Productos $productos, $id)
    {


        // Validar los datos entrantes

        $request->validate([
            "nombre" => "required|max:100",
            "descripcion" => "required|max:100",
            "precio" => "required|numeric",
        ]);

        // Encontrar el producto por ID
        $productos = Productos::findOrFail($id);

        // Actualizar los campos del producto
        $productos->nombre = $request->nombre;
        $productos->descripcion = $request->descripcion;
        $productos->precio = $request->precio;

        // Si se proporciona una nueva imagen, manejar la subida de imagen
        if ($request->hasFile('img')) {
            // Borrar la imagen antigua si existe
            if ($productos->img) {
                $oldImagePath = public_path($productos->img);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Subir la nueva imagen
            $image = $request->file('img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);

            // Actualizar el campo de imagen en la base de datos
            $productos->img = 'images/' . $imageName;
        }

        // Guardar los cambios
        $productos->save();
        return redirect('productos');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Productos $productos, Request $request, $id)
    {
        //

        $productos = Productos::findOrFail($id);
        $productos->delete();
        return redirect('productos');
    }
}
