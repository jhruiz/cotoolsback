<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estadoitem extends Model
{
    /**
     * Se obtiene la información de todos los estados para los items registrados en la aplicacion
     */
    public static function obtenerEstadosItems( ) {
		$data = Estadoitem::select()
                ->get();
    	return $data;      	
    }
}