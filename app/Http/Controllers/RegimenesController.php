<?php

namespace App\Http\Controllers;

use App\Regimene;
use Illuminate\Http\Request;

class RegimenesController extends Controller
{
    
    /**
     * Retorna la información de todos los regimenes registradas en la base de datos
     */
    public function obtenerRegimen()
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen los regimen
            $regimen = Regimene::obtenerRegimen();
            
            // valida si se econtraron registros
            if( !empty( $regimen ) ) {
                $resp['estado'] = true;
                $resp['data'] = $regimen;
            } else {
                $resp['mensaje'] = 'No se encontraron Regimen registrados';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Funcion para guardar un regimen
     */
    public function crearRegimen( Request $request ) {
        
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

                // Crea la regimen
                $id = Regimene::crearRegimen( $data );

                if( $id ) {                        
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Regimen creado correctamente.';
                    $resp['data'] = $id;                            
                } else {
                    $resp['mensaje'] = 'No fue posible crear el regimen';
                }

            } else {
                $resp['mensaje'] = 'La información para creación del regimen no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }

    /**
     * Funcion que actualiza la información de un regimen específico
     */
    public function actualizarRegimen( Request $request ) {
        
        $codigo = $request['codigo'];
        $descripcion = $request['descripcion'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $codigo ) && !empty( $descripcion ) ) {

                // Obtiene la informacion del regimen
                $regimenId = Regimene::obtenerRegimenPorCodigo( $codigo )['0']->id;

                // Actualiza el regimen
                $respCat = Regimene::actualizarRegimen( $regimenId, $descripcion );

                if( $respCat ) {
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Regimen actualizado correctamente.';
                    $resp['data'] = null;                            
                } else {
                    $resp['mensaje'] = 'No fue posible actualizar el regimen.';
                }

            } else {
                $resp['mensaje'] = 'La información para la actualización del regimen no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp; 
    }

}