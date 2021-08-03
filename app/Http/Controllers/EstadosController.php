<?php

namespace App\Http\Controllers;

use App\Estado;
use Illuminate\Http\Request;

class EstadosController extends Controller
{
    
    /**
     * Retorna la información de todos los estados registrados en la base de datos
     */
    public function obtenerEstados()
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen los estados 
            $estados = Estado::obtenerEstados(); 
            
            // valida si se econtraron registros
            if( !empty( $estados ) ) {
                $resp['estado'] = true;
                $resp['data'] = $estados;
            } else {
                $resp['mensaje'] = 'No se encontraron los estados';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }
    
}