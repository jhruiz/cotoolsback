<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estadospedido extends Model
{
    /**
     * Se obtiene la información de todos los estados pedidos configurados en la aplicación
     */
    public static function obtenerEstadosPedido( ) {
		$data = Estadospedido::select()
                    ->get();
    	return $data;     	
    }
}