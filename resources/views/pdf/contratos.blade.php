<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato #{{ $contrato->id }}</title>
    <style>

         body {
            font-family: Verdana, Arial, sans-serif;
            color:rgb(68, 68, 70);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: x-small;
        }
        th, td {
            padding: 6px;
            text-align: left;
        }
        thead {
            background-color: lightgray;
        }
        tfoot tr td {
            font-weight: bold;
            font-size: x-small;
        }
        .header, .footer {
            margin-bottom: 20px;
        }
        .footer td {
            border-top: 4px solid #000;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .gray {
            background-color: lightgray;
        }

        .text-size{
            font-size: 10px;
        }
        .border-top{
            border-top: 2 solid;
        }
        .border-left{
            border-left: 1 solid;
        }
        .border-right{
            border-right: 1 solid;
        }
        .border-bottom{
            border-bottom: 1 solid;
        }
    </style>
</head>
<body>
        <table class="border-bottom">
            <tr>
                <td><img src="{{ asset('images/logo.jpeg') }}" alt="Logo" width="200">
                    <h3>Ruc: 20612890588</h3>
                </td>
                <td>
                    <p class="text-size"><strong>Direccion:</strong>  Asociacion los Warpas MZ "L" LTE - 19 - Ayacucho</p>
                    <p class="text-size"><strong>Contacto:</strong> 958 538 118 / 962 514 007 / 967 075 245</p>
                    <p class="text-size">WWW.GRUPOBELLCOR.COM</p>
                </td>
                <td class="text-right">
                    <h1 class="text-right">CONTRATO</h1>

                        <p class=""><strong>Nro. 000{{ $contrato->id }}</strong></p>
                        <p class="text-size">Fecha: {{ $contrato->created_at}}</p>


                    {{-- <h3>GRUPO BELLCORE CONSTRUCTORA</h3>
                    <p class="text-size">ASOCIACION LOS WARPAS MZ "L" LTE - 19 - AYACUCHO</p>
                    <p class="text-size">CONTACTO: 958 538 118 / 962 514 007 / 967 075 245</p>
                    <p class="text-size">CONTACTO: 958 538 118 / 962 514 007 / 967 075 245</p>
                    <p class="text-size">WWW.GRUPOBELLCOR.COM</p> --}}
                </td>
            </tr>

        </table>
    <div>

        <p style="padding: 0px;"><strong style="padding: 0px;">Estimado Cliente:</strong></p>
        <p class="text-size" style="padding: 0px;">
            Mediante la presente, reciba un cordial saludo a nombre de la empresa
            GRUPO BELLCOR Constructora, atendiendo a su solicitud le remitimos la
            siguiente cotizacion de nuestros servicios:</p>

        <table class="">
            <tr  style="padding: 3px">
                <td style="padding: 3px; width: 100px;"  ><strong>DNI</strong></td>
                <td  style="padding: 3px" class=""><strong>: </strong> {{ $clientes->dni }}</td>
            </tr>
            <tr style="padding: 3px">
                <td  style="padding: 3px"class=""><strong>Cliente</strong></td>
                <td  style="padding: 3px"class=""><strong>:</strong> {{ $clientes->nombre }} {{ $clientes->apellido }}</td>
            </tr>
            <tr  style="padding: 3px">
                <td style="padding: 3px; width: 100px;"  ><strong>TELEFONO</strong></td>
                <td  style="padding: 3px" class=""><strong>: </strong> {{ $clientes->telefono }}</td>
            </tr>
            <tr style="padding: 3px">
                <td style="padding: 3px" class=""><strong>Requerimiento</strong></td>
                <td style="padding: 3px" class=""><strong>: </strong> {{ $contrato->requerimiento }}</td>
            </tr>
            <tr style="padding: 3px">
                <td style="padding: 3px" class=""><strong>Ubicación</strong></td>
                <td style="padding: 3px" class=""><strong>: </strong> {{ $contrato->ubicacion }}</td>
            </tr>
            <tr style="padding: 3px">
                <td style="padding: 3px" class=""><strong>Dep. Prov. Dist.</strong></td>
                <td style="padding: 3px" class=""> <strong>: </strong>{{ $contrato->departamento }}/{{ $contrato->provincia }}/{{ $contrato->distrito }}</td>
            </tr>
        </table>
    </br>

        <table >
            <thead >
                <tr>
                    <th class="text-center ">#</th>
                    <th class="text-center ">Producto</th>
                    <th class="text-center ">Imagen</th>
                    <th class="text-center ">Cantidad</th>
                    <th class="text-center ">Precio Unitario</th>
                    <th class="text-center ">Total</th>
                </tr>
            </thead>
            <tbody >
                @php
                    $i = 1;
                @endphp
                @foreach ($detalles as $detalle)
                <tr >
                    <td class=" ">{{ $i++ }}</td>
                    <td class=" "><p><strong>{{ $productos->find($detalle->productoid)->nombre }}</strong></p>
                                <p>{{ $productos->find($detalle->productoid)->descripcion }}</p>
                    </td>
                    <td class="text-center "><img src="{{ asset($productos->find($detalle->productoid)->img) }}" width="50" height="50"  alt="" srcset=""></td>
                    <td class="text-center ">{{ $detalle->cantidad }}</td>
                    <td class="text-center ">{{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="text-right">{{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
                @endforeach
                <tr class="border-top">
                    <td colspan="4" rowspan="3" style="padding: 0px" ><img src="{{ $qrCodeDataUrl }}" width="100" alt="Código QR"></td>
                    <td >Sub Total</td>
                    <td class=" text-right">S/. {{ number_format( $contrato->total, 2) }}</td>
                </tr>
                <tr >

                    <td >Descuento</td>
                    <td class="text-right">S/.  {{ number_format($contrato->descuento, 2) }}</td>
                </tr>
                <tr >

                    <td ><strong>Total</strong></td>
                    <td class="text-right"><strong> S/.  {{ number_format($contrato->total - $contrato->descuento, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </br>
        <table>
            <thead  >
                <tr >
                    <th >
                        <strong class="text-size">CONDICIONES GENERALES </strong>
                    </th>
                    <th >
                        <strong class="text-size text-center">CONDICIONES DE PAGO </strong>
                </tr>
            </thead>
            <tbody >
                <tr>
                    <th >

                            @foreach ($obscontizaciones as $observaciones )
                            <div class="text-size"><strong>-</strong>
                                {{$obs->find($observaciones->observacionesid)->nombre}}
                            </div>
                            @endforeach
                    </th>
                    <th style="padding: 3px; width: 300px;" >
                        <p>- Primer adelanto: (Reserva del proyecto)</p>
                        <p>- Segundo adelanto 50%: (entrega de material)</p>
                        <p>- Restante a la entrega del proyecto,</p>
                    </th>
                </tr>
            </tbody>
        </table>

    </br>
</br>
</br>
        <table>
            <tbody>
                <tr style="padding: 0; !importand">
                    <th style="padding: 0; !importand" class="text-center ">_______________________________________</th>
                    <th style="padding: 0; !importand" class="text-center ">_______________________________________</th>
                </tr>
                <tr style="padding: 0; !importand">
                    <th style="padding: 0; !importand" class="text-center ">GRUPO BELLCOR</th>
                    <th style="padding: 0; !importand" class="text-center ">CLIENTE</th>
                </tr>
                <tr style="padding: 0; !importand"> text-size
                    <th style="padding: 0; !importand" class="text-center text-size ">RIBEN YAC BELLIDO GUTIERREZ</th>
                    <th style="padding: 0; !importand" class="text-center text-size "></th>
                </tr>
                <tr style="padding: 0; !importand">
                    <th style="padding: 0; !importand" class="text-center  text-size">REPRESENTANTE</th>
                    <th style="padding: 0; !importand" class="text-center text-size "></th>
                </tr>
                <tr >
                    <th  class="text-center  text-size"></th>
                    <th class="text-center text-size "></th>
                </tr>
                <tr >
                    <th  class="text-center  text-size"></th>
                    <th class="text-center text-size "></th>
                </tr>

            </tbody>
        </table>

    </div>

</body>
</html>
