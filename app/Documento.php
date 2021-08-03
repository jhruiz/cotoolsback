<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    /**
     * Se obtiene la información de todos los documentos registrados en la aplicacion
     */
    public static function obtenerDocumentos( ) {
		$data = Documento::select()
                ->get();
    	return $data;      	
    }
}