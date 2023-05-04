<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipopago extends Model
{
    /**
     * Se obtienen los tipos de pagos
     */
    public static function obtenerTiposPagos( ) {
		$data = Tipopago::select()
                ->get();
    	return $data;  
    }
}