<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estadopedido extends Model
{
    /**
     * Se obtiene la información de todos los estados pedidos configurados en la aplicación
     */
    public static function obtenerEstadosPedido( ) {
		$data = Estadopedido::select()
                    ->where('estadopedidos.mostrar', '=', '1') 
                    ->orderBy('orden', 'ASC') 
                    ->get();
    	return $data;     	
    }
}