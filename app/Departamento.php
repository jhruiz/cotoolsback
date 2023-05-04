<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{

  /**
   * Se obtienen todos los departamentos registrados en la base de datos
   */
  public static function obtenerDepartamentos() {
    $data = Departamento::select()
            ->get();
    return $data;  
  }

  /**
   * Se crea un departamento
   */
  public static function crearDepartamento( $data ) {
    $id = Departamento::insertGetId([
      'paise_id' => $data['paise_id'],
      'descripcion' => $data['descripcion'],
      'created_at' => $data['created_at']
    ]);	 
    
    return $id;     
  }

  /**
   * Se obtiene la información de un departamento en particular por su descripcion
   */
  public static function obtenerDptoPorDesc( $descripcion ) {
    $data = Departamento::select()
            ->where('descripcion', $descripcion)
            ->get();
    return $data;      	
  }

  /**
   * Actualiza la información de un departamento específico
   */
  public static function actualizarDepartamento( $paisId, $dptoId ) {
    // obtiene la información de un departamento
    $departamento = Departamento::find($dptoId);
    
    // valida que el departamento exista
    if( !empty( $departamento ) ) {
        $departamento->paise_id = $paisId;
        $departamento->save();

        return true;
    }

    return false;   
  }
  
}