<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Palabrasclave extends Model
{
    /**
     * Obtiene todas las palabras clave registradas para un item
     */
    public static function obtenerPalabrasClaveItem( $itemId ) {
        $data = Palabrasclave::select()                              
                                ->where('palabrasclaves.item_id', $itemId) 
                                ->get();
        return $data;
    }

    /**
     * Crea una palabra clave para un item
     */
    public static function crearPalabraClaveItem( $data ) {

	    $id = Palabrasclave::insertGetId([            
            'palabra' => $data['palabra'],
            'item_id' => $data['itemId'],
            'created_at' => $data['created']
        ]);	 
      
        return $id;  

    }

    /**
     * Elimina una palabra clave específica
     */
    public static function eliminarPalabraClaveItem( $id ) {
        // obtiene la informacion de la palabra clave
        $palabra = Palabrasclave::select()
                    ->where('palabrasclaves.id', $id)
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
        $data = Palabrasclave::select()                              
                                ->where('palabrasclaves.palabra', 'LIKE', "%$descripcion%") 
                                ->get();
        return $data;        
    }

}