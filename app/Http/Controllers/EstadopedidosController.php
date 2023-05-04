<?php

namespace App\Http\Controllers;

use App\Estadopedido;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class EstadopedidosController extends Controller
{
    
    /**
     * Retorna la información de todos los pedidos registrados en la base de datos
     */
    public function obtenerEstadospedidos(){

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen los pedidos
            $estados = Estadopedido::obtenerEstadosPedido(); 
            
            // valida si se econtraron registros
            if( !empty( $estados ) ) {
                $resp['estado'] = true;
                $resp['data'] = $estados;
            } else {
                $resp['mensaje'] = 'No se encontraron los estados.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }
}