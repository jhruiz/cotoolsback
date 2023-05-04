<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Listaprecio extends Model
{
    /**
     * Se obtiene la lista de precios de un item particular
     */
    public static function obtenerListaprecios( $itemId ) {
		$data = Listaprecio::select()
                ->where('item_id', $itemId)
                ->get();
    	return $data;  
    }


    /**
     * Crea una nueva linea
     */
    public static function crearListaPrecios( $data ) {
        $id = Listaprecio::insertGetId([        
            'item_id' => $data['item_id'],
            'precio1' => $data['precio1'],
            'ivaincp1' => $data['ivaincp1'],
            'precio2' => $data['precio2'],
            'ivaincp2' => $data['ivaincp2'],
            'precio3' => $data['precio3'],
            'ivaincp3' => $data['ivaincp3'],
            'precio4' => $data['precio4'],
            'ivaincp4' => $data['ivaincp4'],
            'created_at' => $data['created_at']
        ]);	 
        
        return $id;        
    }

    /**
     * Actualiza la información de la lista de precios de un producto
     */
    public static function actualizarListaPrecios( $itemId, $precio1, $ivaincp1, $precio2, $ivaincp2, $precio3, $ivaincp3, $precio4, $ivaincp4 ) {

        // obtiene la información de la lista de precios
        $listaPrecios = Listaprecio::find( $itemId );
        
        // valida que el impuesto exista
        if( !empty( $listaPrecios ) ) {
            $listaPrecios->precio1  = $precio1;
            $listaPrecios->ivaincp1 = $ivaincp1;
            $listaPrecios->precio2  = $precio2;
            $listaPrecios->ivaincp2 = $ivaincp2;
            $listaPrecios->precio3  = $precio3;
            $listaPrecios->ivaincp3 = $ivaincp3;
            $listaPrecios->precio4  = $precio4;
            $listaPrecios->ivaincp4 = $ivaincp4;
            $listaPrecios->save();

            return true;
        }

        return false;         
    }
}