<?php

namespace App\Http\Controllers;

use App\Models\Contratos;
use Illuminate\Http\Request;
namespace App\Http\Controllers;

use App\Models\Contratos;
use App\Models\Productos;
use App\Models\Clientes;
use App\Models\DetallesContratos;
use App\Models\Observaciones;
use App\Models\Observacionescontratos;
use Illuminate\Http\Request;
use Inertia\Inertia;

use Dompdf\Dompdf;
use Dompdf\Options;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class ContratosController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contratos = Contratos::all();
        $Productos = Productos::all();
        $clientes = Clientes::all();
        $observaciones = Observaciones::all();

        return inertia::render(
            'Contratos/Index',
            [
                'contratos' => $contratos,
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
            "detallecontratos" => "required|array",
            "detallecontratos.*.cantidad" => ["required", "numeric", "regex:/^\d+(\.\d{1,2})?$/"],
            "detallecontratos.*.precio_unitario" => ["required", "numeric", "regex:/^\d+(\.\d{1,2})?$/"],
            "detallecontratos.*.subtotal" => ["required", "numeric", "regex:/^\d+(\.\d{1,2})?$/"],
            "detallecontratos.*.productoid" => "required|exists:productos,id",
        ]);

        $contratos = new Contratos();
        $contratos->total = $request->total;
        $contratos->requerimiento = $request->requerimiento;
        $contratos->ubicacion = $request->ubicacion;
        $contratos->departamento = $request->departamento;
        $contratos->provincia = $request->provincia;
        $contratos->distrito = $request->distrito;
        $contratos->descuento = $request->descuento;
        $contratos->clienteid = $request->clienteid;

        $contratos->save();
        $idIngresado = $contratos->id;

        foreach ($request->detallecontratos as $detalle) {
            $detalleCotizacion = new DetallesContratos();
            $detalleCotizacion->cantidad = $detalle["cantidad"];
            $detalleCotizacion->precio_unitario = $detalle["precio_unitario"];
            $detalleCotizacion->subtotal = $detalle["subtotal"];
            $detalleCotizacion->contratoid = $idIngresado;
            $detalleCotizacion->productoid = $detalle["productoid"];
            $detalleCotizacion->save();
        }

        $contratos = Contratos::all();
        $Productos = Productos::all();
        $clientes = Clientes::all();
        $observaciones = Observaciones::all();

        foreach($request->obscheck as $obs){
            $obschecks = new Observacionescontratos();
            $obschecks->observacionesid=$obs;
            $obschecks->contratosid=$idIngresado;
            $obschecks->save();
        }
        return inertia::render(
            'Contratos/Show',
            [
                'contratos' => $contratos,
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
    public function show(Contratos $contratos, $id)
    {
        //
        $cotrato = Contratos::findOrFail($id);
        return Inertia::render('Contratos/Index', [
            'cotizacion' => $cotrato
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contratos $contratos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contratos $contratos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contratos $contratos)
    {
        //
    }


    public function generarPDF($id)
    {
        // Obtener los datos necesarios para el PDF
        $contrato = Contratos::findOrFail($id);
        $detalles = DetallesContratos::where('contratoid', $id)->get();
        $productos = Productos::all();
        $clientes = Clientes::where('id', $contrato->clienteid)->firstOrFail();
        $obscontizaciones = Observacionescontratos::where('contratosid', $id)->get();
        $obs = Observaciones::all();

        $url = route('contratos.pdf', ['id' => $id]);

        // Generar el código QR en memoria
        $qrCode = new QrCode($url);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Obtener el contenido de la imagen QR
        $qrCodeDataUrl = 'data:image/png;base64,' . base64_encode($result->getString());

        // Renderizar la vista a HTML
        $html = view('pdf.contratos', compact('contrato', 'detalles', 'productos', 'clientes', 'qrCodeDataUrl','obscontizaciones','obs'))->render();

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
