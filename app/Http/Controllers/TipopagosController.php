<?php

namespace App\Http\Controllers;

use App\Tipopago;
use Illuminate\Http\Request;

class TipopagosController extends Controller
{
    
    /**
     * Retorna la información de todos los tipos de pagos registrados en la base de datos
     */
    public function obtenerTipopagos( )
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen los tipos de pagos
            $tipoPagos = Tipopago::obtenerTiposPagos();
            
            // valida si se econtraron registros
            if( !empty( $tipoPagos ) ) {
                $resp['estado'] = true;
                $resp['data'] = $tipoPagos;
            } else {
                $resp['mensaje'] = 'No se encontraron tipos de pagos registrados.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

}