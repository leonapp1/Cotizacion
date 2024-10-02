<?php

namespace App\Http\Controllers;

use App\Models\Cotizaciones;
use App\Models\Productos;
use App\Models\Clientes;
use App\Models\DetallesCotizacion;
use App\Models\Observaciones;
use App\Models\Observacionescotizaciones;
use Illuminate\Http\Request;
use Inertia\Inertia;

use Dompdf\Dompdf;
use Dompdf\Options;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class CotizacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cotizaciones = Cotizaciones::all();
        $Productos = Productos::all();
        $clientes = Clientes::all();
        $observaciones = Observaciones::all();
        return inertia::render(
            'Cotizaciones/Index',
            [
                'cotizaciones' => $cotizaciones,
                'productos' => $Productos,
                "clientes" => $clientes,
                "observaciones" => $observaciones,
                'valor' => "0",
            ]
        );
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

        $request->validate([
            "clienteid" => "required|exists:clientes,id",
            "total" => ["required", "numeric", "regex:/^\d+(\.\d{1,2})?$/"],
            "descuento" => ["required", "numeric", "regex:/^\d+(\.\d{1,2})?$/"],
            "requerimiento" => "required|max:100",
            "ubicacion" => "required|max:100",
            "departamento" => "required|max:100",
            "provincia" => "required|max:100",
            "distrito" => "required|max:100",
            "detallecotizacion" => "required|array",
            "detallecotizacion.*.cantidad" => ["required", "numeric", "regex:/^\d+(\.\d{1,2})?$/"],
            "detallecotizacion.*.precio_unitario" => ["required", "numeric", "regex:/^\d+(\.\d{1,2})?$/"],
            "detallecotizacion.*.subtotal" => ["required", "numeric", "regex:/^\d+(\.\d{1,2})?$/"],
            "detallecotizacion.*.productoid" => "required|exists:productos,id",
        ]);

        $cotizaciones = new Cotizaciones();
        $cotizaciones->total = $request->total;
        $cotizaciones->requerimiento = $request->requerimiento;
        $cotizaciones->ubicacion = $request->ubicacion;
        $cotizaciones->departamento = $request->departamento;
        $cotizaciones->provincia = $request->provincia;
        $cotizaciones->distrito = $request->distrito;
        $cotizaciones->descuento = $request->descuento;
        $cotizaciones->clienteid = $request->clienteid;

        $cotizaciones->save();
        $idIngresado = $cotizaciones->id;

        foreach ($request->detallecotizacion as $detalle) {
            $detalleCotizacion = new DetallesCotizacion();
            $detalleCotizacion->cantidad = $detalle["cantidad"];
            $detalleCotizacion->precio_unitario = $detalle["precio_unitario"];
            $detalleCotizacion->subtotal = $detalle["subtotal"];
            $detalleCotizacion->cotizacionid = $idIngresado;
            $detalleCotizacion->productoid = $detalle["productoid"];
            $detalleCotizacion->save();
        }

        $cotizaciones = Cotizaciones::all();
        $Productos = Productos::all();
        $clientes = Clientes::all();
        $observaciones = Observaciones::all();

        foreach($request->obscheck as $obs){
            $obschecks = new Observacionescotizaciones();
            $obschecks->observacionesid=$obs;
            $obschecks->cotizacionesid=$idIngresado;
            $obschecks->save();
        }
        return inertia::render(
            'Cotizaciones/Index',
            [
                'cotizaciones' => $cotizaciones,
                'productos' => $Productos,
                "clientes" => $clientes,
                'valor' => $idIngresado,
                "observaciones" => $observaciones,
            ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Cotizaciones $cotizaciones, $id)
    {
        //
        $cotizacion = Cotizaciones::findOrFail($id);
        return Inertia::render('Cotizaciones/Index', [
            'cotizacion' => $cotizacion
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cotizaciones $cotizaciones)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cotizaciones $cotizaciones)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cotizaciones $cotizaciones)
    {
        //
    }


    public function generarPDF($id)
    {
        // Obtener los datos necesarios para el PDF
        $cotizacion = Cotizaciones::findOrFail($id);
        $detalles = DetallesCotizacion::where('cotizacionid', $id)->get();
        $productos = Productos::all();
        $clientes = Clientes::where('id', $cotizacion->clienteid)->firstOrFail();
        $obscontizaciones = Observacionescotizaciones::where('cotizacionesid', $id)->get();
        $obs = Observaciones::all();

        $url = route('cotizaciones.pdf', ['id' => $id]);

        // Generar el código QR en memoria
        $qrCode = new QrCode($url);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Obtener el contenido de la imagen QR
        $qrCodeDataUrl = 'data:image/png;base64,' . base64_encode($result->getString());

        // Renderizar la vista a HTML
        $html = view('pdf.cotizacion', compact('cotizacion', 'detalles', 'productos', 'clientes', 'qrCodeDataUrl','obscontizaciones','obs'))->render();

        // Configurar opciones de Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true);

        // Crear una instancia de Dompdf y cargar el HTML generado
        $dompdf = new Dompdf($options);
        $dompdf->setBasePath(public_path());
        $dompdf->loadHtml($html);

        // Renderizar el PDF
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Devolver el PDF como una respuesta HTTP
        return $dompdf->stream('cotizacion_' . $id . '.pdf', ['Attachment' => 0]);
    }
}
