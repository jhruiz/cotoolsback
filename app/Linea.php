<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Linea extends Model
{
    /**
     * Se obtiene el tipo de item por codigo
     */
    public static function obtenerLineas( ) {
		$data = Linea::select()
                ->get();
    	return $data;  
    }

    /**
     * Se obtiene el tipo de item por codigo
     */
    public static function obtenerLineaPorCodigo( $codigo ) {
		  $data = Linea::select()
              ->where('codigo', $codigo)
              ->get();
    	return $data;  
    }

    /**
     * Crea una nueva linea
     */
    public static function crearLinea( $data ) {
      $id = Linea::insertGetId([        
        'codigo' => $data['codigo'],
        'descripcion' => $data['descripcion'],
        'created_at' => $data['created_at']
      ]);	 
      
      return $id;        
    }
}
