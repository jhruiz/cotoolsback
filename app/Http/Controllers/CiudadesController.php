<?php

namespace App\Http\Controllers;

use App\Ciudade;
use App\Departamento;
use Illuminate\Http\Request;

class CiudadesController extends Controller
{
    
    /**
     * Retorna la información de todas las ciudades registradas en la base de datos
     */
    public function obtenerCiudades(Request $request)
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {
            // se obtienen las ciudades
            $ciudades = !isset($request['dptoId']) ? Ciudade::obtenerCiudades() : Ciudade::obtenerCiudadesPorDpto( $request['dptoId'] );

            // valida si se econtraron registros
            if( !empty( $ciudades ) ) {
                $resp['estado'] = true;
                $resp['data'] = $ciudades;
            } else {
                $resp['mensaje'] = 'No se encontraron ciudades';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Funcion para guardar una ciudad
     */
    public function crearCiudad(Request $request) {
        
        $descripcion = $request['descripcion'];
        $departamento = $request['departamento'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $descripcion ) && !empty( $departamento ) ) {

                // Se obtiene la informacion del departamento
                $dptoId = Departamento::obtenerDptoPorDesc($departamento)['0']->id;

                if( !empty($dptoId) ) {
                    $data = array(
                        'departamento_id' => $dptoId,
                        'descripcion' => $descripcion,                    
                        'created_at' => date('Y-m-d H:i:s')
                    );
    
                    // Crea la ciudad
                    $id = Ciudade::crearCiudad( $data );
    
                    if( $id ) {                        
                        $resp['estado'] = true;
                        $resp['mensaje'] = 'Ciudad creada correctamente.';
                        $resp['data'] = $id;                            
                    } else {
                        $resp['mensaje'] = 'No fue posible crear la ciudad';
                    }
                } else {
                    $resp['mensaje'] = 'No fue posible obtener el departamento ' . $departamento; 
                }

            } else {
                $resp['mensaje'] = 'La información para creación de la ciudad no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }

    /**
     * Funcion que actualiza la información de una ciudad específica
     */
    public function actualizarCiudad(Request $request) {
        
        $ciudad = $request['descripcion'];
        $departamento = $request['descripcion'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $ciudad ) && !empty($departamento) ) {

                // Obtiene la informacion de la ciudad
                $ciudadId = Ciudade::obtenerCiudadPorDesc($ciudad)['0']->id;

                // Obtiene la información del departamento
                $dptoId = Departamento::obtenerDptoPorDesc($departamento)['0']->id;

                // Actualiza la ciudad
                $respCat = Ciudade::actualizarCiudad( $ciudadId, $dptoId );

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