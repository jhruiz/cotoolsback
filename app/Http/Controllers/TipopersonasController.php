<?php

namespace App\Http\Controllers;

use App\Tipopersona;
use Illuminate\Http\Request;

class TipopersonasController extends Controller
{
    
    /**
     * Retorna la información de todos los tipos de personas registrados en la base de datos
     */
    public function obtenerTipospersonas()
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen los tipos de personas
            $tipoPersonas = Tipopersona::obtenerTipospersonas();
            
            // valida si se econtraron registros
            if( !empty( $tipoPersonas ) ) {
                $resp['estado'] = true;
                $resp['data'] = $tipoPersonas;
            } else {
                $resp['mensaje'] = 'No se encontraron tipos de personas registrados';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Funcion para guardar un tipo de persona
     */
    public function crearTipopersona( Request $request ) {
        
        $descripcion = $request['descripcion'];
        $codigo = $request['codigo'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $descripcion ) && !empty( $codigo ) ) {

                $data = array(
                    'codigo' => $codigo,
                    'descripcion' => $descripcion,
                    'created_at' => date('Y-m-d H:i:s')
                );

                // Crea el tipo de persona
                $id = Tipopersona::crearTipopersona( $data );

                if( $id ) {                        
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Tipo persona creado correctamente.';
                    $resp['data'] = $id;                            
                } else {
                    $resp['mensaje'] = 'No fue posible crear el tipo de persona.';
                }

            } else {
                $resp['mensaje'] = 'La información para creación del tipo de persona no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }

    /**
     * Funcion que actualiza la información de un tipo de persona específico
     */
    public function actualizarTipopersona( Request $request ) {
        
        $codigo = $request['codigo'];
        $descripcion = $request['descripcion'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $codigo ) && !empty( $descripcion ) ) {

                // Obtiene la informacion del tipo de persona
                $tipPersonaId = Tipopersona::obtenerTipoPersonaPorCodigo( $codigo )['0']->id;

                // Actualiza el tipo de persona
                $respCat = Tipopersona::actualizarTipopersona( $tipPersonaId, $descripcion );

                if( $respCat ) {
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Tipo persona actualizado correctamente.';
                    $resp['data'] = null;                            
                } else {
                    $resp['mensaje'] = 'No fue posible actualizar el tipo de persona.';
                }

            } else {
                $resp['mensaje'] = 'La información para la actualización del tipo de persona no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp; 
    }

}