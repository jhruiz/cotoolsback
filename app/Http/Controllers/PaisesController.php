<?php

namespace App\Http\Controllers;

use App\Paise;
use Illuminate\Http\Request;

class PaisesController extends Controller
{
    
    /**
     * Retorna la información de todas las lineas registradas en la base de datos
     */
    public function obtenerPaises()
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen las paises
            $paises = Paise::obtenerPaises();
            
            // valida si se econtraron registros
            if( !empty( $paises ) ) {
                $resp['estado'] = true;
                $resp['data'] = $paises;
            } else {
                $resp['mensaje'] = 'No se encontraron paises';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

}