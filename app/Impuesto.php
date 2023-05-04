<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Impuesto extends Model
{

  /**
   * Se obtiene el impuesto por el id
   */
  public static function obtenerImpuestoId( $id ) {
    $data = Impuesto::select()
            ->where('id', $id)
            ->get();
    return $data;
  }

  /**
   * Se obtiene la información los impuestos registrados en la base de datos
   */
  public static function obtenerImpuestos( ) {
    $data = Impuesto::select()
            ->get();
    return $data;      	
  }

  /**
   * Obtiene la información de un impuesto en particular por medio del código
   */
  public static function obtenerImptoPorCodigo( $codigo ) {
    $data = Impuesto::select()
            ->where('codigo', $codigo)
            ->get();
    return $data;         
  }

  /**
   * Crea un impuesto
   */
  public static function crearImpuesto( $data ) {
    $id = Impuesto::insertGetId([
      'codigo' => $data['codigo'],
      'descripcion' => $data['descripcion'],
      'tasa' => $data['tasa'],
      'tipoimpuesto_id' => $data['tipoimpuesto_id'],
      'created_at' => $data['created_at']
    ]);	 
    
    return $id;     
  }

  /**
   * Actualiza la información de un impuesto en particular
   */
  public static function actualizarImpuesto( $imptoId, $descripcion, $tasa, $tipoImptoId ) {
        // obtiene la información del impuesto
        $impuesto = Impuesto::find( $imptoId );
        
        // valida que el impuesto exista
        if( !empty( $impuesto ) ) {
            $impuesto->descripcion = $descripcion;
            $impuesto->tasa = $tasa;
            $impuesto->tipoImptoId = $tipoImptoId;
            $impuesto->save();

            return true;
        }

        return false; 
  }

}
