<?php

namespace App\Http\Controllers;

use App\Tipodocumento;
use Illuminate\Http\Request;

class TipodocumentosController extends Controller
{
    
    /**
     * Retorna la información de todos los tipos de documentos registradas en la base de datos
     */
    public function obtenerTiposdocumentos()
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen los tipos de documentos
            $tipoDocs = Tipodocumento::obtenerTiposdocumentos();
            
            // valida si se econtraron registros
            if( !empty( $tipoDocs ) ) {
                $resp['estado'] = true;
                $resp['data'] = $tipoDocs;
            } else {
                $resp['mensaje'] = 'No se encontraron tipos de documentos registrados';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Funcion para guardar un regimen
     */
    public function crearTipodocumento( Request $request ) {
        
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

                // Crea el tipo de documento
                $id = Tipodocumento::crearTipodocumento( $data );

                if( $id ) {                        
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Tipo documento creado correctamente.';
                    $resp['data'] = $id;                            
                } else {
                    $resp['mensaje'] = 'No fue posible crear el tipo de documento.';
                }

            } else {
                $resp['mensaje'] = 'La información para creación del tipo de documento no se encuentra completa.';
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

                // Obtiene la informacion del tipo de documento
                $tipDocId = Tipodocumento::obtenerTipodocumentoPorCodigo( $codigo )['0']->id;

                // Actualiza el tipo de documento
                $respCat = Tipodocumento::actualizarTipodocumento( $tipDocId, $descripcion );

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