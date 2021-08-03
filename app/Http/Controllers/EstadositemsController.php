<?php

namespace App\Http\Controllers;

use App\Estadositem;
use Illuminate\Http\Request;

class EstadosItemsController extends Controller
{
    
    /**
     * Retorna la información de todos los estados de los items registrados en la base de datos
     */
    public function obtenerEstadosItems()
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen los estados 
            $estados = Estadositem::obtenerEstadosItems(); 
            
            // valida si se econtraron registros
            if( !empty( $estados ) ) {
                $resp['estado'] = true;
                $resp['data'] = $estados;
            } else {
                $resp['mensaje'] = 'No se encontraron los estados para los items';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

}