<?php

namespace App\Http\Controllers;

use App\Clasegrupo;
use Illuminate\Http\Request;

class ClasegruposController extends Controller
{
    
    /**
     * Retorna la información de todas las clases de grupos registradas en la base de datos
     */
    public function obtenerClasesgrupos()
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen las clases de grupos
            $claseGrupos = Clasegrupo::obtenerClasesGrupos();
            
            // valida si se econtraron registros
            if( !empty( $claseGrupos ) ) {
                $resp['estado'] = true;
                $resp['data'] = $claseGrupos;
            } else {
                $resp['mensaje'] = 'No se encontraron las clases de los grupos registradas.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

}