<?php

namespace App\Http\Controllers;

use App\Palabrasclave;
use Illuminate\Http\Request;

class PalabrasclavesController extends Controller
{
    
    /**
     * Retorna la información de todas las palabras clave registrados para un item en la base de datos
     */
    public function obtenerPalabrasClaveItem(Request $request)
    {
        $itemId = $request['itemId'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen las palabras clave del item
            $palabrasClave = Palabrasclave::obtenerPalabrasClaveItem($itemId);           

            // valida si se econtraron registros
            if( !empty( $palabrasClave ) ) {
                $resp['estado'] = true;
                $resp['data'] = $palabrasClave;
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Crea una palabra clave para un item especifico
     */
    public function crearPalabraClaveItem(Request $request) {
        
        $itemId = $request['itemId'];
        $palabra = $request['palabra'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $itemId ) && !empty( $palabra ) ) {

                $data = array(
                    'itemId' => $itemId,
                    'palabra' => $palabra,
                    'estado' => 1,
                    'created' => date('Y-m-d H:i:s')
                );

                // Crea el usuario
                $id = Palabrasclave::crearPalabraClaveItem( $data );

                if( $id ) {
                    $resp['estado'] = true;
                    $resp['data'] = $id;   
                    $resp['mensaje'] = 'Palabra creada exitosamente.';                         
                } else {
                    $resp['mensaje'] = 'No fue posible crear la palabra clave para el item';
                }

            } else {
                $resp['mensaje'] = 'La información para creación de la palabra clave no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;
    }

    /**
     * Elimina una palabra clave específica
     */
    public function eliminarPalabrasClaveItem(Request $request) {

        $palabraId = $request['palabraId'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // Valida que se haya enviado un id
            if( !empty( $palabraId ) ) {

                // Valida si fue posible eliminar la palabra clave
                if( Palabrasclave::eliminarPalabraClaveItem( $palabraId ) ) {
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Palabra eliminada exitosamente.';                         
                } else {
                    $resp['mensaje'] = 'No fue posible eliminar la palabra clave para el item';
                }

            } else {
                $resp['mensaje'] = 'La información para eliminar la palabra clave no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;
    }
}