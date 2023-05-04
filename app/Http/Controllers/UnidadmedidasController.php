<?php

namespace App\Http\Controllers;

use App\Unidadmedida;
use Illuminate\Http\Request;

class UnidadmedidasController extends Controller
{
    
    /**
     * Retorna la información de todas las unidades de medidas registrados en la base de datos
     */
    public function obtenerUnidadmedidas()
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen las unidades de medidas
            $unidadMedidas = Unidadmedida::obtenerUnidadmedida();
            
            // valida si se econtraron registros
            if( !empty( $unidadMedidas ) ) {
                $resp['estado'] = true;
                $resp['data'] = $unidadMedidas;
            } else {
                $resp['mensaje'] = 'No se encontraron las unidades de medidas registradas registrados';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Funcion para guardar unidades de medida
     */
    public function crearUnidadmedida( Request $request ) {
        
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

                // Crea la unidad de medida
                $id = Unidadmedida::crearUnidadmedida( $data );

                if( $id ) {
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Unidad de medida creada correctamente.';
                    $resp['data'] = $id;                            
                } else {
                    $resp['mensaje'] = 'No fue posible crear la unidad de medida.';
                }

            } else {
                $resp['mensaje'] = 'La información para creación de la unidad de medida no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }

    /**
     * Funcion que actualiza la información de un tipo de persona específico
     */
    public function actualizarUnidadmedida( Request $request ) {
        
        $codigo = $request['codigo'];
        $descripcion = $request['descripcion'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $codigo ) && !empty( $descripcion ) ) {

                // Obtiene la informacion del tipo de persona
                $unidadMedId = Unidadmedida::obtenerunidadMedidaPorCodigo( $codigo )['0']->id;

                // Actualiza el tipo de persona
                $respCat = Unidadmedida::actualizarUnidadmedida( $unidadMedId, $descripcion );

                if( $respCat ) {
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Unidad de medida actualizada correctamente.';
                    $resp['data'] = null;                            
                } else {
                    $resp['mensaje'] = 'No fue posible actualizar la unidad de medida.';
                }

            } else {
                $resp['mensaje'] = 'La información para la actualización de la unidad de medida no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp; 
    }

}