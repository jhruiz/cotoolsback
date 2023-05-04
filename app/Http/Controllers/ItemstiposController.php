<?php

namespace App\Http\Controllers;

use App\Itemstipo;
use Illuminate\Http\Request;

class ItemstiposController extends Controller
{
    
    /**
     * Retorna la información de todos los tipos de items registradas en la base de datos
     */
    public function obtenerItemstipos()
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen los tipos de items
            $itemstipos = Itemstipo::obtenerItemstipos();
            
            // valida si se econtraron registros
            if( !empty( $itemstipos ) ) {
                $resp['estado'] = true;
                $resp['data'] = $itemstipos;
            } else {
                $resp['mensaje'] = 'No se encontraron tipos de los items';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Funcion para guardar un departamento
     */
    public function crearItemstipo( Request $request ) {
        
        $descripcion = $request['descripcion'];
        $codigo = $request['codigo'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $descripcion ) && !empty( $codigo ) ) {

                $data = array(
                    'descripcion' => $descripcion,                    
                    'codigo' => $codigo,                    
                    'created_at' => date('Y-m-d H:i:s')
                );

                // Crea el tipo de item
                $id = Itemstipo::crearItemstipo( $data );

                if( $id ) {                        
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Tipo de item creado correctamente.';
                    $resp['data'] = $id;                            
                } else {
                    $resp['mensaje'] = 'No fue posible crear el tipo de item';
                }

            } else {
                $resp['mensaje'] = 'La información para creación del tipo de item no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }

    /**
     * Funcion que actualiza la información de un departamento específico
     */
    public function actualizarItemstipo( Request $request ) {
        
        $codigo = $request['codigo'];
        $descripcion = $request['descripcion'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $codigo ) && !empty( $descripcion ) ) {

                // Obtiene la informacion del tipo de item
                $itemTipoId = Itemstipo::obtenerItemstipoPorCodigo( $codigo )['0']->id;

                // Actualiza el tipo de item
                $respCat = Itemstipo::actualizarItemstipo( $itemTipoId, $descripcion );

                if( $respCat ) {
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Tipo de item actualizado correctamente.';
                    $resp['data'] = null;                            
                } else {
                    $resp['mensaje'] = 'No fue posible actualizar el tipo de item.';
                }

            } else {
                $resp['mensaje'] = 'La información para la actualización del tipo de item no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp; 
    }

}