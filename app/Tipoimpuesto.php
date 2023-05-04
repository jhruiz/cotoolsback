<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipoimpuesto extends Model
{

  /**
   * Se obtiene la información de todos los tipos de impuestos registrados en la base de datos
   */
  public static function obtenerTiposimpuestos( ) {
    $data = Tipoimpuesto::select()          
            ->get();
    return $data; 
  }

  /**
   * Se obtiene la información de un tipo de impuesto particular registrado en la base de datos
   */
  public static function obtenerTipoImptoPorCodigo( $codigo ) {
    $data = Tipoimpuesto::select()
            ->where('codigo', $codigo)
            ->get();
    return $data;      	
  }

  /**
   * Se crea un nuevo registro de tipo de impuesto
   */
  public static function crearTipoimpuesto( $data ) {
    $id = Tipoimpuesto::insertGetId([
      'codigo' => $data['codigo'],
      'descripcion' => $data['descripcion'],
      'created_at' => $data['created_at']
    ]);	 
    
    return $id;    
  }

  /**
   * Se actualiza la descripcion de un tipo de impuesto específico
   */
  public static function actualizarTipoimpuesto( $tipImptoId, $descripcion ) {
    // obtiene la información del tipo de impuesto
    $tipImpto = Tipoimpuesto::find( $tipImptoId );
    
    // valida que el tipo de impuesto exista
    if( !empty( $tipImpto ) ) {
        $tipImpto->descripcion = $descripcion;
        $tipImpto->save();

        return true;
    }

    return false; 
  }
  
}