<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unidadmedida extends Model
{
    /**
     * Se obtiene el tipo de item por codigo
     */
    public static function obtenerUnidadmedida( ) {
		$data = Unidadmedida::select()
                ->get();
    	return $data;  
    }

    /**
     * Obtiene una unidad de medida específica por código
     */
    public static function obtenerunidadMedidaPorCodigo( $codigo ) {
        $data = Unidadmedida::select()
                ->where('codigo', $codigo)
                ->get();
        return $data;
    }

    /**
     * Crea una nueva unidad de medida
     */
    public static function crearUnidadmedida( $data ) {
        $id = Unidadmedida::insertGetId([
            'codigo' => $data['codigo'],
            'descripcion' => $data['descripcion'],
            'created_at' => $data['created_at']
          ]);	 
          
        return $id;        
    }

    /**
     * Actualiza la descripción de una unidad de medida específica
     */
    public static function actualizarUnidadmedida( $unidadMedId, $descripcion ) {
        // obtiene la información de la unidad de medida
        $unidadM = Unidadmedida::find( $unidadMedId );
        
        // valida que la unidad de medida exista
        if( !empty( $unidadM ) ) {
            $unidadM->descripcion = $descripcion;
            $unidadM->save();

            return true;
        }

        return false;  
    }
}
