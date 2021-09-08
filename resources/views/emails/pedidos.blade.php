<html>
    <head>
        <style>
            #customers {
                font-family: Arial, Helvetica, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }

            #customers td, #customers th {
                border: 1px solid #ddd;
                padding: 8px;
            }

            #customers tr:nth-child(even){background-color: #f2f2f2;}

            #customers tr:hover {background-color: #ddd;}

            #customers th {
                padding-top: 12px;
                padding-bottom: 12px;
                text-align: left;
                background-color: #767574;
                color: white;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid">

            <div style="text-align: center">
                <h3>Su pedido fue procesado exitósamente.</span></h3>
                <h4>Detalles del pedido.</span></h4>
            </div>
            
            <div>
                <h4><span>Número del pedido: {{ $data['0']->nro_pdweb }}</span></h4>
                <h4><span>Fecha del pedido: {{ $data['0']->created }}</span></h4>
            </div>

            <div>
                <!-- Seccion para el detalle del pedido -->
                <table id="customers">
                        <thead>
                            <tr>        
                                <th>Item</th>       
                                <th>Cantidad</th>       
                                <th>Vlr. Unit.</th>     
                                <th>Imp. %.</th>        
                                <th>Subtotal</th>       
                            </tr>       
                        </thead>
                        <tbody>
                            @foreach ($data as $dat)
                                <tr>
                                    <td style="text-align: left">{!! $dat->descripcion !!}</td>
                                    <td style="text-align: center">{{ $dat->cantidad }}</td>
                                    <td style="text-align: right">{{ number_format($dat->precioventaunit, 2) }}</td>
                                    <td style="text-align: center">{{ $dat->tasaiva }} %</td>
                                    <td style="text-align: right">{{ number_format($dat->baseTtal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody>
                            <tr>
                                <th colspan="4" style="text-align: right">Subtotal neto</th>
                                <td style="text-align: right"> {{ number_format($data->subTtalNeto, 2) }} </td>
                            </tr>

                            <tr>
                                <th colspan="4" style="text-align: right">IVA</th>
                                <td  style="text-align: right"> {{ number_format($data->iva, 2) }} </td>
                            </tr>

                            <tr>
                                <th colspan="4" style="text-align: right">Total a pagar</th>
                                <td style="text-align: right"> {{ number_format($data->ttalPagar, 2) }} </td>
                            </tr>                 
                        </tbody>
                </table>                
                <!-- fin seccion para el detalle del pedido -->
            </div>

            <div style="text-align: center">
                <h3>Para nosotros es importante siempre contar contigo como nuestro socio de negocio.</span></h3>
            </div>            

        </div>
    </body>
</html>