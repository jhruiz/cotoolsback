<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    /**
     * Se obtiene la información de todos los usuarios registrados en la aplicacion
     */
    public static function obtenerUsuarios( ) {
		$data = Usuario::select('usuarios.id', 'usuarios.nombre', 'usuarios.identificacion',
                                'usuarios.email', 'usuarios.username', 'usuarios.estado_id',
                                'perfiles.id as perfile_id','perfiles.descripcion as perfil', 'estados.id as estado_id',
                                'estados.descripcion as estado')
                ->leftjoin('perfiles_usuarios', 'perfiles_usuarios.usuario_id', '=', 'usuarios.id')       
                ->leftjoin('perfiles', 'perfiles.id', '=', 'perfiles_usuarios.perfile_id')   
                ->leftjoin('estados', 'estados.id', '=', 'usuarios.estado_id')
                ->get();
    	return $data;      	
    }

    /**
     * Se obtiene la información de un usuario especifico en la base de datos
     */
    public static function obtenerUsuario( $id ) {
      $data = Usuario::select('usuarios.id', 'usuarios.nombre', 'usuarios.identificacion',
                              'usuarios.email', 'usuarios.username', 'usuarios.estado_id',
                              'perfiles.id as perfile_id','perfiles.descripcion as perfil', 'estados.id as estado_id',
                              'estados.descripcion as estado')                              
                ->leftjoin('perfiles_usuarios', 'perfiles_usuarios.usuario_id', '=', 'usuarios.id')       
                ->leftjoin('perfiles', 'perfiles.id', '=', 'perfiles_usuarios.perfile_id')   
                ->leftjoin('estados', 'estados.id', '=', 'usuarios.estado_id')      
                ->where('usuarios.id', $id) 
                ->get();
    	return $data;      	
    }

    /**
     * Obtiene la información de un usuario por medio del email
     */
    public static function obtenerUsuarioPorEmail( $email ) {
      $data = Usuario::select()
              ->where('email', $email)
              ->get();
      return $data;
    }

    /**
     * Obtiene la información de un usuario por medio de su username
     */
    public static function obtenerUsuarioPorUsername( $usuario ) {
      $data = Usuario::select()
              ->where('username', $usuario)
              ->get();
      return $data;
    }

    /**
     * Crea un usuario en estado pendiente por verificación
     */
    public static function crearUsuario( $data ) {

	    $id = Usuario::insertGetId([
        'nombre' => $data['nombre'],
        'identificacion' => $data['identificacion'],
        'email' => $data['email'],
        'username' => $data['email'],
        'password' => $data['contrasenia'],
        'estado_id' => 1,
        'created' => $data['created']
      ]);	 
      
      return $id;  

    }

    /**
     * Crea los usuarios obtenidos de datax
     */
    public static function sincronizarUsuario( $data ) {

      try {
        $id = Usuario::insertGetId([
          'nombre' => $data['nombre'],
          'identificacion' => $data['identificacion'],
          'email' => $data['email'],
          'username' => $data['email'],
          'password' => $data['contrasenia'],
          'estado_id' => 2,
          'created' => $data['created']
        ]);	 
        
        return $id;

      } catch(Throwable $e) {
        return false;
      }

    }

    /**
     * Actualizar el estado del usuario a activo
     */
    public static function actualizarUsuario( $id, $estadoId ) {

      // obtiene la informacion del usuario
      $usuario = Usuario::select()
                  ->where('usuarios.id', $id)
                  ->get();

      if(!empty($usuario['0']->id)) {
        $usuario['0']->estado_id = $estadoId;
        $usuario['0']->save();

        return true;
      }
      
      return false;
    }

}