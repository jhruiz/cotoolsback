<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriasGrupo extends Model
{
    /**
     * Se crea una nueva categoria
     */
    public static function crearCategoriaGrupos( $catId, $grId, $date ) {
        $id = CategoriasGrupo::insertGetId([
            'categoria_id' => $catId,
            'grupo_id' => $grId,
            'created_at' => $date
          ]);	 
          
        return $id;  
    }

    /**
     * Obtiene los grupos asociados a una categoria
     */
    public static function obtenerGruposCategoria( $id ){
		$data = CategoriasGrupo::select()
                ->join('grupos', 'grupos.id', '=', 'categorias_grupos.grupo_id')
                ->where('categorias_grupos.categoria_id', $id)
                ->get();
    	return $data;          
    }

    /**
     * Elimina los grupos asociados a una categoría específica
     */
    public static function eliminarCategoriasGrupos( $categoriaId ) {
      // Obtiene el usuario por id
      $catGrupos = CategoriasGrupo::select()
                  ->where('categoria_id', $categoriaId)
                  ->get();

      // Verifica si el usuario existe para eliminarlo
      if( !empty($catGrupos['0']->id) ) {
          foreach( $catGrupos as $cat ) {
              $cat->delete();
          }
      }
  }    
        
}