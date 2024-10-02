<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use Illuminate\Http\Request;
use Inertia\Inertia;
use GuzzleHttp\Client;

class ClientesController extends Controller
{

    private $client;
    private $apiToken;
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->client = new Client();
    }
    public function index()
    {
        $clientes = Clientes::all();
        return inertia::render('Clientes/Index', ['clientes' => $clientes]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia::render('Clientes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "dni" => "required|max:100",
            "nombre" => "required|max:100",
            "apellido" => "required|max:100",
            "email" => "required|max:100",
            "telefono" => "required|max:100",
            "direccion" => "required|max:100",
        ]);
        $clientes = new Clientes($request->input());
        $clientes->save();
        return redirect("clientes");
    }

    /**
     * Display the specified resource.
     */
    public function show(Clientes $clientes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Clientes $clientes)
    {
        return inertia::render('Clientes/Edit', ['clientes' => $clientes]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Clientes $clientes, $id)

    {

        $request->validate([
            "dni" => "required|max:100",
            "nombre" => "required|max:100",
            "apellido" => "required|max:100",
            "email" => "required|max:100",
            "telefono" => "required|max:100",
            "direccion" => "required|max:100",
        ]);

        $clientes = Clientes::findOrFail($id);
        $clientes->update($request->all());
        return redirect('clientes');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Clientes $clientes, $id)
    {
        $clientes = Clientes::findOrFail($id);
        $clientes->delete();
        return redirect('clientes');
    }

    public function searchByDni(Request $request)
    {
        $dni = $request->input('dni');
        $token = 'apis-token-9508.FwxV2d2vGeoux4A1NVJGcxlFhp0036IL';
        if (strlen($dni) == 8) {
            $link = 'https://apis.net.pe/api-consulta-dni';
        }
        if (strlen($dni) == 11) {
            $link = 'https://apis.net.pe/v2/sunat/ruc';
        }
        $client = new Client(['base_uri' => 'https://api.apis.net.pe', 'verify' => false]);

        $parameters = [
            'http_errors' => false,
            'connect_timeout' => 5,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Referer' => $link,
                'User-Agent' => 'laravel/guzzle',
                'Accept' => 'application/json',
            ],
            'query' => ['numero' => $dni]
        ];
        if (strlen($dni) == 8) {
            $res = $client->request('GET', '/v2/reniec/dni', $parameters);
        }
        if (strlen($dni) == 11) {
            $res = $client->request('GET', '/v2/sunat/ruc', $parameters);
        }
        $data = json_decode($res->getBody()->getContents(), true);

        return response()->json($data);
    }
}
