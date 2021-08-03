<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\CategoriasGrupo;
use Illuminate\Http\Request;

class CategoriasController extends Controller
{
    
    /**
     * Retorna la información de todas las categorias registradas en la base de datos
     */
    public function obtenerCategorias()
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen las categorias
            $categorias = Categoria::obtenerCategorias(); 
            
            // valida si se econtraron registros
            if( !empty( $categorias ) ) {
                $resp['estado'] = true;
                $resp['data'] = $categorias;
            } else {
                $resp['mensaje'] = 'No se encontraron las categorias';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Funcion para guardar una categoria
     */
    public function guardarCategoria(Request $request) {
        
        $descripcion = $request['descripcion'];
        $grupos = $request['grupos'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $descripcion ) ) {

                $data = array(
                    'descripcion' => $descripcion,
                    'created' => date('Y-m-d H:i:s')
                );

                // Crea la categoria
                $id = Categoria::crearCategoria( $data );

                if( $id ) {

                    foreach($grupos as $gr) {
                        $arrGr = explode("_", $gr);
                        CategoriasGrupo::crearCategoriaGrupos($id, $arrGr['1'], date('Y-m-d H:i:s'));
                    }                            

                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Categoría creada correctamente.';
                    $resp['data'] = $id;                            
                } else {
                    $resp['mensaje'] = 'No fue posible crear la categoria';
                }

            } else {
                $resp['mensaje'] = 'La información para creación de la cateogoría no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }


    /**
     * Funcion para obtener una categoria especifica
     */
    public function obtenerCategoria(Request $request) {
        
        $categoriaId = $request['categoriaId'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $categoriaId ) ) {

                // Obtiene la categoria
                $categoria = Categoria::obtenerCategoria( $categoriaId );

                if( $categoria ) {
                    $resp['estado'] = true;
                    $resp['data'] = $categoria;                            
                } else {
                    $resp['mensaje'] = 'No fue posible obtener la categoria';
                }

            } else {
                $resp['mensaje'] = 'La información para obtener la cateogoría no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp; 
    } 

    /**
     * Funcion que actualiza la información de una categoría específica
     */
    public function actualizarCategoria(Request $request) {
        
        $descripcion = $request['descripcion'];
        $grupos = $request['grupos'];
        $categoriaId = $request['categoriaId'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $descripcion ) ) {

                // Actualiza la categoria
                $respCat = Categoria::actualizarCategoria( $categoriaId, $descripcion );

                if( $respCat ) {

                    // Se eliminan los grupos asociados a la categoria
                    CategoriasGrupo::eliminarCategoriasGrupos( $categoriaId );

                    // Se agregan los grupos a la categoría
                    foreach($grupos as $gr) {
                        $arrGr = explode("_", $gr);
                        CategoriasGrupo::crearCategoriaGrupos($categoriaId, $arrGr['1'], date('Y-m-d H:i:s'));
                    }

                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Categoría actualizada correctamente.';
                    $resp['data'] = null;                            
                } else {
                    $resp['mensaje'] = 'No fue posible actualizar la categoria';
                }

            } else {
                $resp['mensaje'] = 'La información para la actualización de la cateogoría no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp; 
    }

}