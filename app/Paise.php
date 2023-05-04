<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paise extends Model
{

  /**
   * Se obtiene la información de un pais en particular por su descripcion
   */
  public static function obtenerPaisPorDesc( $descripcion ) {
    $data = Paise::select()
            ->where('descripcion', $descripcion)
            ->get();
    return $data;      	
  }

  /**
   * Se obtiene la información de todos los paises registrados en la base de datos
   */
  public static function obtenerPaises( ) {
    $data = Paise::select()
            ->get();
    return $data;    
  }
  
}