<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Configuraciondato extends Model
{
    /**
     * Se obtiene el dato de configuracion por nombre
     */
    public static function obtenerConfiguracion( $nombre ) {
		  $data = Configuraciondato::select()
                ->where('nombre', $nombre)
                ->get();
    	return $data;      	
    }
}