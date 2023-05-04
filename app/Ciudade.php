<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ciudade extends Model
{

    /**
     * Se obtienen las ciudades
     */
    public static function obtenerCiudades( ) {
		$data = Ciudade::select()
                ->get();
    	return $data;      	
    }  

    /**
     * Se obtiene una ciudad por su descripcion
     */
    public static function obtenerCiudadPorDesc( $descripcion ) {
		$data = Ciudade::select()
                ->where('descripcion', $descripcion)
                ->get();
    	return $data;      	
    }    

    /**
     * Se crea una nueva ciudad
     */
    public static function crearCiudad( $data ) {
        $id = Ciudade::insertGetId([
            'departamento_id' => $data['departamento_id'],
            'descripcion' => $data['descripcion'],
            'created_at' => $data['created_at']
          ]);	 
          
        return $id;  
    }

    /**
     * Actualiza el departamento de una ciudad
     */
    public static function actualizarCiudad( $ciudadId, $dptoId ) {
        // obtiene la información de la ciudad
        $ciudad = Ciudade::find($ciudadId);
        
        // valida que la ciudad exista
        if( !empty( $ciudad ) ) {
            $ciudad->departamento_id = $dptoId;
            $ciudad->save();

            return true;
        }

        return false;        
    }

    /**
     * Obtiene una ciudad por id
     */
    public static function obtenerCiudadPorId( $id ) {
        // obtiene la información de la ciudad
        $ciudad = Ciudade::find($id);
        return $ciudad;      
    }
}