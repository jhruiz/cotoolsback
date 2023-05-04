<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipopersona extends Model
{
    /**
     * Se obtienen los tipos de personas
     */
    public static function obtenerTipospersonas( ) {
		$data = Tipopersona::select()
                ->get();
    	return $data;  
    }

    /**
     * Obtiene un tipo de personas específico por código
     */
    public static function obtenerTipoPersonaPorCodigo( $codigo ) {
        $data = Tipopersona::select()
                ->where('codigo', $codigo)
                ->get();
        return $data; 
    }    

    /**
     * Crear un nuevo tipo de persona
     */
    public static function crearTipopersona( $data ) {
        $id = Tipopersona::insertGetId([
            'codigo' => $data['codigo'],
            'descripcion' => $data['descripcion'],
            'created_at' => $data['created_at']
          ]);	 
          
        return $id;        
    }

    /**
     * Actualizar la descripcion de un tipo de persona en particular
     */
    public static function actualizarTipopersona( $tipPersonaId, $descripcion ) {
        // obtiene la información del tipo de persona
        $tipoPersona = Tipopersona::find( $tipPersonaId );
        
        // valida que el regimen exista
        if( !empty( $tipoPersona ) ) {
            $tipoPersona->descripcion = $descripcion;
            $tipoPersona->save();

            return true;
        }

        return false;  
    }

}