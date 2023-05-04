<?php

namespace App\Http\Controllers;

use App\Impuesto;
use App\Tipoimpuesto;
use Illuminate\Http\Request;

class ImpuestosController extends Controller
{
    
    /**
     * Retorna la información de todos los impuestos registrados en la base de datos
     */
    public function obtenerImpuestos()
    {
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen los impuestos
            $impuestos = Impuesto::obtenerImpuestos();
            
            // valida si se econtraron registros
            if( !empty( $impuestos ) ) {
                $resp['estado'] = true;
                $resp['data'] = $grupos;
            } else {
                $resp['mensaje'] = 'No se encontraron impuestos';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Funcion para guardar un impuesto
     */
    public function crearImpuesto( Request $request ) {

        $codigo = $request['codigo'];
        $descripcion = $request['descripcion'];
        $tasa = $request['tasa'];
        $tipoimpuesto = $request['tipoimpuesto'];
        
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $codigo ) && !empty( $descripcion ) && !empty( $tasa ) && !empty( $tipoImpuesto ) ) {

                // Se obtiene la informacion del tipo de impuesto
                $tipoImptoId = Tipoimpuesto::obtenerTipoImptoPorCodigo( $tipoImpuesto )['0']->id;

                if( !empty( $tipoImptoId ) ) {
                    $data = array(
                        'codigo' => $codigo,
                        'descripcion' => $descripcion,                    
                        'tasa' => $tasa,                    
                        'tipoimpuesto_id' => $tipoImptoId,                    
                        'created_at' => date('Y-m-d H:i:s')
                    );
    
                    // Crea el timpuesto
                    $id = Impuesto::crearImpuesto( $data );
    
                    if( $id ) {                        
                        $resp['estado'] = true;
                        $resp['mensaje'] = 'Impuesto creado correctamente.';
                        $resp['data'] = $id;                            
                    } else {
                        $resp['mensaje'] = 'No fue posible crear el impuesto';
                    }
                } else {
                    $resp['mensaje'] = 'No fue posible obtener el tipo de impuesto ' . $clase; 
                }

            } else {
                $resp['mensaje'] = 'La información para creación del impuesto no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }

    /**
     * Funcion que actualiza la información de un impuesto específico
     */
    public function actualizarImpuesto( Request $request ) {
        
        $codigo = $request['codigo'];
        $descripcion = $request['descripcion'];
        $tasa = $request['tasa'];
        $tipoImpuesto = $request['tipoImpuesto'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $codigo ) && !empty( $descripcion ) && !empty( $tasa ) && !empty( $tipoImpuesto ) ) {

                // Obtiene la información del impuesto
                $imptoId = Impuesto::obtenerImptoPorCodigo( $codigo )['0']->id;

                // Se obtiene la informacion del tipo de impuesto
                $tipoImptoId = Tipoimpuesto::obtenerTipoImptoPorCodigo( $tipoImpuesto )['0']->id;

                // Actualiza el impuesto
                $respCat = Impuesto::actualizarImpuesto( $imptoId, $descripcion, $tasa, $tipoImptoId );

                if( $respCat ) {
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Ciudad actualizada correctamente.';
                    $resp['data'] = null;                            
                } else {
                    $resp['mensaje'] = 'No fue posible actualizar la ciudad';
                }

            } else {
                $resp['mensaje'] = 'La información para la actualización de la ciudad no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp; 
    }

}