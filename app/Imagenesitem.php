<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Imagenesitem extends Model
{
    /**
     * Se obtienen las imágenes de un ítem
     */
    public static function obtenerImagenesItem( $itemId ) {
		  $data = Imagenesitem::select()
                ->where('item_id', $itemId)
                ->get();
    	return $data;      	
    }

    /**
     * Guarda una imagen relacionada a un ítem
     */
    public static function guardarImagenesItem( $data ) {

      $id = Imagenesitem::insertGetId([
          'url' => $data['url'],
          'item_id' => $data['item_id'],
          'posicion' => $data['posicion'],
          'created_at' => $data['created_at']
        ]);	
        
      return $id; 
    } 

    /**
     * Actualiza el estado de una imagen específica
     */
    public static function actualizarEstado( $value, $imagenId ) {
      // obtiene la informacion de la imagen
      $imagen = Imagenesitem::find($imagenId);
      
      // valida que la imagen exista
      if( !empty( $imagen ) ) {
        $imagen->estado_id = $value;
        $imagen->save();

        return true;
      }

      return false;
    }

    /**
     * Actualiza el estado que destaca una imagen específica
     */
    public static function actualizarEstadoImagen( $value, $imagenId ) {
      // obtiene la informacion de la imagen
      $imagen = Imagenesitem::find($imagenId);
      
      // valida que la imagen exista
      if( !empty( $imagen ) ) {
        $imagen->estadoitem_id = $value;
        $imagen->save();

        return true;
      }

      return false;
    }

    /**
     * Elimina una imagen específica
     */
    public static function eliminarImagenItem( $id ) {
      // obtiene la informacion de la imagen
      $imagen = Imagenesitem::select()
                  ->where('imagenesitems.id', $id)
                  ->get();

      if(!empty($imagen['0']->id)) {
          $imagen['0']->delete();

          return true;
      }
      
      return false;
  }    

  /**
   * Obtiene una imagen para un item específico
   */
  public static function obtenerImagenItem($itemId) {
    $data = Imagenesitem::select()
          ->where('item_id', $itemId)
          ->take(1)
          ->get();
    return $data;   
  }
}