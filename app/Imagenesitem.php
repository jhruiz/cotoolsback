<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Imagenesitem extends Model
{
    /**
     * Se obtienen las imágenes de un ítem
     */
    public static function obtenerImagenesItem( $codigoItem ) {
		  $data = Imagenesitem::select()
                ->where('cod_item', $codigoItem)
                ->get();
    	return $data;      	
    }

    /**
     * Guarda una imagen relacionada a un ítem
     */
    public static function guardarImagenesItem( $data ) {

      $id = Imagenesitem::insertGetId([
          'url' => $data['url'],
          'cod_item' => $data['cod_item'],
          'posicion' => $data['posicion'],
          'estadoitem_id' => $data['estadoitem_id'],
          'estado_id' => $data['estado_id'],
          'created' => $data['created']
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
  public static function obtenerImagenItem($item) {
    $data = Imagenesitem::select()
          ->where('cod_item', $item)
          ->take(1)
          ->get();
    return $data;   
  }
}