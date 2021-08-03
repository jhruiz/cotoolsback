<?php

namespace App\Http\Controllers;

use App\Perfile;
use Illuminate\Http\Request;

class PerfilesController extends Controller
{
    
    /**
     * Retorna la información de todos los perfiles registrados en la base de datos
     */
    public function obtenerPerfiles()
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen los perfiles
            $perfiles = Perfile::obtenerPerfiles(); 
            
            // valida si se econtraron registros
            if( !empty( $perfiles ) ) {
                $resp['estado'] = true;
                $resp['data'] = $perfiles;
            } else {
                $resp['mensaje'] = 'No se encontraron los perfiles';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

}