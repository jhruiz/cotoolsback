<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PerfilesUsuario extends Model
{

    /**
     * Crea la relacion del perfil y el usuario
     */
    public static function crearPerfilUsuario($perfilId, $usuarioId, $created) {
	    $id = PerfilesUsuario::insertGetId([
            'perfile_id' => $perfilId,
            'usuario_id' => $usuarioId,
            'created' => $created
          ]);	
          
        return $id;  
    }

    /**
     * Elimina los perfiles relacionados a un usuario especifico.
     */
    public static function eliminarPerfilesUsuario($usuarioId) {

        // Obtiene el usuario por id
        $perfilesU = PerfilesUsuario::select()
                    ->where('usuario_id', $usuarioId)
                    ->get();

        // Verifica si el usuario existe para eliminarlo
        if( !empty($perfilesU['0']->id) ) {
            foreach( $perfilesU as $perfil ) {
                $perfil->delete();
            }
        }
    }
}