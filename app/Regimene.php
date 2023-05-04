<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Regimene extends Model
{
    /**
     * Se obtiene el tipo de item por codigo
     */
    public static function obtenerRegimen( ) {
		$data = Regimene::select()
                ->get();
    	return $data;  
    }

    /**
     * Se obtiene un regimen por código
     */
    public static function obtenerRegimenPorCodigo( $codigo ) {
        $data = Regimene::select()
                ->where('codigo', $codigo)
                ->get();
        return $data; 
    }

    /**
     * Craear nuevo regimen
     */
    public static function crearRegimen( $data ) {
        $id = Regimene::insertGetId([
            'codigo' => $data['codigo'],
            'descripcion' => $data['descripcion'],
            'created_at' => $data['created_at']
          ]);	 
          
        return $id;
    }

    /**
     * Actualiza la descripción de un régimen específico
     */
    public static function actualizarRegimen( $regimenId, $descripcion ) {
        // obtiene la información del regimen
        $regimen = Regimen::find( $regimenId );
        
        // valida que el regimen exista
        if( !empty( $regimen ) ) {
            $regimen->descripcion = $descripcion;
            $regimen->save();

            return true;
        }

        return false;          
    }
}
    