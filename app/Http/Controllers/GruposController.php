<?php

namespace App\Http\Controllers;

use App\Grupo;
use App\Clasegrupo;
use Illuminate\Http\Request;

class GruposController extends Controller
{
    
    /**
     * Retorna la información de todos los grupos registradas en la base de datos
     */
    public function obtenerGrupos()
    {
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen los grupos
            $grupos = Grupo::obtenerGrupos();

            // valida si se econtraron registros
            if( !empty( $grupos ) ) {
                $resp['estado'] = true;
                $resp['data'] = $grupos;
            } else {
                $resp['mensaje'] = 'No se encontraron grupos';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Funcion para guardar un grupo
     */
    public function crearGrupo( Request $request ) {

        $grupo = $request['grupo'];
        $subgrupo = $request['subgrupo'];
        $descripcion = $request['descripcion'];
        $clase = $request['clase'];
        
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $grupo ) && !empty( $subgrupo ) && !empty( $descripcion ) && !empty( $clase ) ) {

                // Se obtiene la informacion de la clase del grupo
                $claseId = Clasegrupo::obtenerClasePorDesc( $clase )['0']->id;

                if( !empty( $claseId ) ) {
                    $data = array(
                        'grupo' => $grupo,
                        'subgrupo' => $subgrupo,                    
                        'descripcion' => $descripcion,                    
                        'clasegrupo_id' => $claseId,                    
                        'created_at' => date('Y-m-d H:i:s')
                    );
    
                    // Crea el grupo
                    $id = Grupo::crearGrupo( $data );
    
                    if( $id ) {                        
                        $resp['estado'] = true;
                        $resp['mensaje'] = 'Grupo creado correctamente.';
                        $resp['data'] = $id;                            
                    } else {
                        $resp['mensaje'] = 'No fue posible crear el grupo';
                    }
                } else {
                    $resp['mensaje'] = 'No fue posible obtener la clase del grupo ' . $clase; 
                }

            } else {
                $resp['mensaje'] = 'La información para creación del grupo no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }

    /**
     * Funcion que actualiza la información de un grupo específico
     */
    public function actualizarGrupo( Request $request ) {
        
        $grupo = $request['grupo'];
        $subgrupo = $request['subgrupo'];
        $descripcion = $request['descripcion'];
        $clase = $request['clase'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $grupo ) && !empty( $subgrupo ) && !empty( $descripcion ) && !empty( $clase ) ) {

                // Obtiene la información del grupo
                $grupoId = Grupo::obtenerGrupoPorDesc( $descripcion )['0']->id;

                // Se obtiene la informacion de la clase del grupo
                $claseId = Clasegrupo::obtenerClasePorDesc( $clase )['0']->id;

                // Actualiza el grupo
                $respCat = Grupo::actualizarGrupo( $grupo, $subgrupo, $claseId, $grupoId );

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