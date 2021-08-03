<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Palabrasclaveitem extends Model
{
    /**
     * Obtiene todas las palabras clave registradas para un item
     */
    public static function obtenerPalabrasClaveItem( $itemId ) {
        $data = Palabrasclaveitem::select()                              
                                ->where('palabrasclaveitems.cod_item', $itemId) 
                                ->get();
        return $data;
    }

    /**
     * Crea una palabra clave para un item
     */
    public static function crearPalabraClaveItem( $data ) {

	    $id = Palabrasclaveitem::insertGetId([
            'cod_item' => $data['itemId'],
            'palabra' => $data['palabra'],
            'estado_id' => $data['estado'],
            'created' => $data['created']
        ]);	 
      
        return $id;  

    }

    /**
     * Elimina una palabra clave específica
     */
    public static function eliminarPalabraClaveItem( $id ) {
        // obtiene la informacion de la palabra clave
        $palabra = Palabrasclaveitem::select()
                    ->where('palabrasclaveitems.id', $id)
                    ->get();

        if(!empty($palabra['0']->id)) {
            $palabra['0']->delete();

            return true;
        }
        
        return false;
    }

    /**
     * Obtiene los productos relacionados a una palabra clave
     */
    public static function obtenerItemsPorPC( $descripcion ) {
        $data = Palabrasclaveitem::select()                              
                                ->where('palabrasclaveitems.palabra', 'LIKE', "%$descripcion%") 
                                ->get();
        return $data;        
    }

}