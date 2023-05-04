<?php

namespace App\Http\Controllers;

use App\Tipoimpuesto;
use Illuminate\Http\Request;

class TipoimpuestosController extends Controller
{
    
    /**
     * Retorna la información de todos los tipos de impuestos registrados en la base de datos
     */
    public function obtenerTiposimpuestos()
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen los tipos de documentos
            $tipoImpto = Tipoimpuesto::obtenerTiposimpuestos();
            
            // valida si se econtraron registros
            if( !empty( $tipoImpto ) ) {
                $resp['estado'] = true;
                $resp['data'] = $tipoImpto;
            } else {
                $resp['mensaje'] = 'No se encontraron tipos de impuestos registrados';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Funcion para guardar un tipo de impuesto
     */
    public function crearTipoimpuesto( Request $request ) {
        
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

                // Crea el tipo de impuesto
                $id = Tipoimpuesto::crearTipoimpuesto( $data );

                if( $id ) {                        
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Tipo impuesto creado correctamente.';
                    $resp['data'] = $id;                            
                } else {
                    $resp['mensaje'] = 'No fue posible crear el tipo de impuesto.';
                }

            } else {
                $resp['mensaje'] = 'La información para creación del tipo de impuesto no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }

    /**
     * Funcion que actualiza la información de un tipo de documento específico
     */
    public function actualizarTipodocumento( Request $request ) {
        
        $codigo = $request['codigo'];
        $descripcion = $request['descripcion'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $codigo ) && !empty( $descripcion ) ) {

                // Obtiene la informacion del tipo de impuesto
                $tipImptoId = Tipoimpuesto::obtenerTipoImptoPorCodigo( $codigo )['0']->id;

                // Actualiza el tipo de impuesto
                $respCat = Tipoimpuesto::actualizarTipoimpuesto( $tipImptoId, $descripcion );

                if( $respCat ) {
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Tipo documento actualizado correctamente.';
                    $resp['data'] = null;                            
                } else {
                    $resp['mensaje'] = 'No fue posible actualizar el tipo de documento.';
                }

            } else {
                $resp['mensaje'] = 'La información para la actualización del tipo de documento no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp; 
    }

}