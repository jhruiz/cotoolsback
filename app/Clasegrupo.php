<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clasegrupo extends Model
{

  /**
   * Se obtiene la información de una clase de grupo en particular por su descripcion
   */
  public static function obtenerClasePorDesc( $descripcion ) {
    $data = Clasegrupo::select()
            ->where('descripcion', $descripcion)
            ->get();
    return $data;      	
  }

  /**
   * Obtiene todas las clases de grupos registrados en la base de datos
   */
  public static function obtenerClasesGrupos( ) {
    $data = Clasegrupo::select()
            ->get();
    return $data;
  }
  
}