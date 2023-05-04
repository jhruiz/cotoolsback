<?php

namespace App\Http\Controllers;

use App\Departamento;
use App\Paise;
use Illuminate\Http\Request;

class DepartamentosController extends Controller
{
    
    /**
     * Retorna la información de todos los departamentos registradas en la base de datos
     */
    public function obtenerDepartamentos()
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen los departamentos
            $departamentos = Departamento::obtenerDepartamentos();
            
            // valida si se econtraron registros
            if( !empty( $departamentos ) ) {
                $resp['estado'] = true;
                $resp['data'] = $departamentos;
            } else {
                $resp['mensaje'] = 'No se encontraron departamentos';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Funcion para guardar un departamento
     */
    public function crearDepartamento( Request $request ) {
        
        $descripcion = $request['descripcion'];
        $pais = $request['pais'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $descripcion ) && !empty( $pais ) ) {

                // Se obtiene la informacion del pais
                $paisId = Paise::obtenerPaisPorDesc( $pais )['0']->id;

                if( !empty($paisId) ) {
                    $data = array(
                        'paise_id' => $paisId,
                        'descripcion' => $descripcion,                    
                        'created_at' => date('Y-m-d H:i:s')
                    );
    
                    // Crea el departamento
                    $id = Departamento::crearDepartamento( $data );
    
                    if( $id ) {                        
                        $resp['estado'] = true;
                        $resp['mensaje'] = 'Departamento creado correctamente.';
                        $resp['data'] = $id;                            
                    } else {
                        $resp['mensaje'] = 'No fue posible crear el departamento';
                    }
                } else {
                    $resp['mensaje'] = 'No fue posible obtener el pais ' . $pais; 
                }

            } else {
                $resp['mensaje'] = 'La información para creación del departamento no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }

    /**
     * Funcion que actualiza la información de un departamento específico
     */
    public function actualizarDepartamento( Request $request ) {
        
        $pais = $request['pais'];
        $departamento = $request['descripcion'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $pais ) && !empty( $departamento ) ) {

                // Obtiene la informacion del pais
                $paisId = Paise::obtenerPaisPorDesc( $pais )['0']->id;

                // Obtiene la información del departamento
                $dptoId = Departamento::obtenerDptoPorDesc( $departamento )['0']->id;

                // Actualiza el departamento
                $respCat = Departamento::actualizarDepartamento( $paisId, $dptoId );

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