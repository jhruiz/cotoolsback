<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Itemstipo extends Model
{
    /**
     * Se obtiene el tipo de item por codigo
     */
    public static function obtenerItemstipoPorCodigo( $codigo ) {
		$data = Itemstipo::select()
                ->where('codigo', $codigo)
                ->get();
    	return $data;  
    }
    
    /**
     * Se crea un nuevo tipo de item
     */
    public static function crearItemstipo( $data ) {
        $id = Itemstipo::insertGetId([
            'descripcion' => $data['descripcion'],
            'codigo' => $data['codigo'],
            'created_at' => $data['created_at']
          ]);	 
          
        return $id;         
    }

    /**
     * Actualiza la información de un tipo de item específico
     */
    public static function actualizarItemstipo( $itemTipoId, $descripcion ) {
        // obtiene la información de la ciudad
        $itemsTipo = Itemstipo::find($itemTipoId);
        
        // valida que la ciudad exista
        if( !empty( $itemsTipo ) ) {
            $itemsTipo->descripcion = $descripcion;
            $itemsTipo->save();

            return true;
        }

        return false;  
    }
}