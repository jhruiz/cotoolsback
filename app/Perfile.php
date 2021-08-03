<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perfile extends Model
{
    /**
     * Se obtiene la información de todos los perfiles registrados en la aplicacion
     */
    public static function obtenerPerfiles( ) {
		$data = Perfile::select()
                ->get();
    	return $data;      	
    }
}