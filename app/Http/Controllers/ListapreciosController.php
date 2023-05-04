<?php

namespace App\Http\Controllers;

use App\Listaprecio;
use App\Item;
use Illuminate\Http\Request;

class ListapreciosController extends Controller
{
    
    /**
     * Retorna la información de todas las listas de precios registradas en la base de datos
     */
    public function obtenerListasPrecios( $itemId )
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        if(!empty( $itemId )) {
            try {
    
                // se obtienen las listas de los precios de un item particular
                $listaPrecios = Listaprecio::obtenerListaprecios( $itemId );
                
                // valida si se econtraron registros
                if( !empty( $listaPrecios ) ) {
                    $resp['estado'] = true;
                    $resp['data'] = $listaPrecios;
                } else {
                    $resp['mensaje'] = 'No se encontraron listas de precios para el producto';
                }
    
            } catch(Throwable $e) {
                return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
            }
        } else {
            $resp['mensaje'] = 'Debe seleccionar un producto para obtener la lista de precios correspondiente.';
        }

        return $resp;

    }

    /**
     * Funcion para guardar un departamento
     */
    public function crearListaPrecios( Request $request ) {
        
        $item_id = $request['item_id'];
        $precio1 = $request['precio1'];
        $ivaincp1 = $request['ivaincp1'];
        $precio2 = $request['precio2'];
        $ivaincp2 = $request['ivaincp2'];
        $precio3 = $request['precio3'];
        $ivaincp3 = $request['ivaincp3'];
        $precio4 = $request['precio4'];
        $ivaincp4 = $request['ivaincp4'];
        
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {
          
            if( !empty( $item_id ) ) {

                $data = array(
                    'item_id' => $item_id,
                    'precio1' => $precio1,
                    'ivaincp1' => $ivaincp1,
                    'precio2' => $precio2,
                    'ivaincp2' => $ivaincp2,
                    'precio3' => $precio3,
                    'ivaincp3' => $ivaincp3,
                    'precio4' => $precio4,
                    'ivaincp4' => $ivaincp4,
                    'created_at' => date('Y-m-d H:i:s')
                );

                // Crea la linea
                $id = Listaprecio::crearListaPrecios( $data );

                if( $id ) {                        
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Linea creada correctamente.';
                    $resp['data'] = $id;                            
                } else {
                    $resp['mensaje'] = 'No fue posible crear la linea';
                }

            } else {
                $resp['mensaje'] = 'La información para creación la linea no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }

    /**
     * Funcion que actualiza la información de una lista de precios específico
     */
    public function actualizarListaPrecio( Request $request ) {
        
        $referencia = $request['referencia'];
        $precio1 = $request['precio1'];
        $ivaincp1 = $request['ivaincp1'];
        $precio2 = $request['precio2'];
        $ivaincp2 = $request['ivaincp2'];
        $precio3 = $request['precio3'];
        $ivaincp3 = $request['ivaincp3'];
        $precio4 = $request['precio4'];
        $ivaincp4 = $request['ivaincp4'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $referencia ) ) {

                // Obtiene la informacion del item
                $itemId = Item::obtenerItemPorReferencia( $referencia )['0']->id;

                // Actualiza la lista de precios
                $respCat = Listaprecio::actualizarListaPrecios( $itemId, $precio1, $ivaincp1, $precio2, $ivaincp2, $precio3, $ivaincp3, $precio4, $ivaincp4 );

                if( $respCat ) {
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Lista de precios actualizada correctamente.';
                    $resp['data'] = null;                            
                } else {
                    $resp['mensaje'] = 'No fue posible actualizar la lista de precios.';
                }

            } else {
                $resp['mensaje'] = 'La información para la actualización la lista de precios no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp; 
    }

}