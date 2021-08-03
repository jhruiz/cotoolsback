<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    /**
     * Se obtiene la información de todos los estados registrados en la aplicacion
     */
    public static function obtenerEstados( ) {
		$data = Estado::select()
                ->get();
    	return $data;      	
    }
}