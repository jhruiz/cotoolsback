<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estadositem extends Model
{
    /**
     * Se obtiene la información de todos los estados para los items registrados en la aplicacion
     */
    public static function obtenerEstadosItems( ) {
		$data = Estadositem::select()
                ->get();
    	return $data;      	
    }
}