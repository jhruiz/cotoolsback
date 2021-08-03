<?php

namespace App\Http\Controllers;

use App\Documento;
use Illuminate\Http\Request;

class DocumentosController extends Controller
{
    
    /**
     * Retorna la información de todos los documentos registrados en la base de datos
     */
    public function obtenerDocumentos()
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen los documentos 
            $documentos = Documento::obtenerDocumentos(); 
            
            // valida si se econtraron registros
            if( !empty( $documentos ) ) {
                $resp['estado'] = true;
                $resp['data'] = $documentos;
            } else {
                $resp['mensaje'] = 'No se encontraron los documentos';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

}