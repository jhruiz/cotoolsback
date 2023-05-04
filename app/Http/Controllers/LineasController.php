<?php

namespace App\Http\Controllers;

use App\Linea;
use Illuminate\Http\Request;

class LineasController extends Controller
{
    
    /**
     * Retorna la información de todas las lineas registradas en la base de datos
     */
    public function obtenerLineas()
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen las lineas
            $lineas = Linea::obtenerLineas();
            
            // valida si se econtraron registros
            if( !empty( $lineas ) ) {
                $resp['estado'] = true;
                $resp['data'] = $lineas;
            } else {
                $resp['mensaje'] = 'No se encontraron lineas';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Funcion para guardar un departamento
     */
    public function crearLinea( Request $request ) {
        
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

                // Crea la linea
                $id = Linea::crearLinea( $data );

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
     * Funcion que actualiza la información de un departamento específico
     */
    public function actualizarLinea( Request $request ) {
        
        $codigo = $request['codigo'];
        $descripcion = $request['descripcion'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $codigo ) && !empty( $descripcion ) ) {

                // Obtiene la informacion de la linea
                $lineaId = Linea::obtenerLineaPorCodigo( $codigo )['0']->id;

                // Actualiza la linea
                $respCat = Linea::actualizarLinea( $lineaId, $descripcion );

                if( $respCat ) {
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Linea actualizada correctamente.';
                    $resp['data'] = null;                            
                } else {
                    $resp['mensaje'] = 'No fue posible actualizar la linea.';
                }

            } else {
                $resp['mensaje'] = 'La información para la actualización la linea no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp; 
    }

}