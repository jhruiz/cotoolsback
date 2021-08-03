<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    /**
     * Se obtienen las categorias
     */
    public static function obtenerCategorias( ) {
		$data = Categoria::select()
                ->get();
    	return $data;      	
    }

    /**
     * Se obtiene una categoria especifica
     */
    public static function obtenerCategoria( $id ) {
		$data = Categoria::select()
                ->where('Categorias.id', $id)
                ->get();
    	return $data;  
    }

    /**
     * Se crea una nueva categoria
     */
    public static function crearCategoria( $data ) {
        $id = Categoria::insertGetId([
            'descripcion' => $data['descripcion'],
            'created' => $data['created']
          ]);	 
          
        return $id;  
    }

    /**
     * Actualiza la información de una categoría específica
     */
    public static function actualizarCategoria( $categoriaId, $descripcion ) {
      // obtiene la información de la categoría
      $categoria = Categoria::find($categoriaId);
      
      // valida que la categoría exista
      if( !empty( $categoria ) ) {
        $categoria->descripcion = $descripcion;
        $categoria->save();

        return true;
      }

      return false;
    }
        
}