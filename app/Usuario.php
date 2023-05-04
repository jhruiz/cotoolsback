<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    /**
     * Se obtiene la información de todos los usuarios registrados en la aplicacion
     */
    public static function obtenerUsuarios( ) {
		  $data = Usuario::select('usuarios.id', 'usuarios.primer_nombre', 'usuarios.segundo_nombre', 
                              'usuarios.primer_apellido', 'usuarios.segundo_apellido','usuarios.nit',
                              'usuarios.email', 'usuarios.estado_id',
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
      $data = Usuario::select('usuarios.id', 'usuarios.primer_nombre', 'usuarios.segundo_nombre', 
                              'usuarios.primer_apellido', 'usuarios.segundo_apellido','usuarios.nit',
                              'usuarios.direccion', 'usuarios.telefono','usuarios.celular',
                              'usuarios.email', 'usuarios.estado_id', 'tipodocumentos.descripcion as tipo_documento',
                              'perfiles.id as perfile_id','perfiles.descripcion as perfil', 'estados.id as estado_id',
                              'estados.descripcion as estado', 'tipopersonas.descripcion as tipo_persona', 'regimenes.descripcion as regimen',
                              'ciudades.descripcion as ciudad')
                ->leftjoin('perfiles_usuarios', 'perfiles_usuarios.usuario_id', '=', 'usuarios.id')       
                ->leftjoin('perfiles', 'perfiles.id', '=', 'perfiles_usuarios.perfile_id')   
                ->leftjoin('estados', 'estados.id', '=', 'usuarios.estado_id')      
                ->leftjoin('tipodocumentos', 'tipodocumentos.id', '=', 'usuarios.tipodocumento_id')
                ->leftjoin('tipopersonas', 'tipopersonas.id', '=', 'usuarios.tipopersona_id')
                ->leftjoin('regimenes', 'regimenes.id', '=', 'usuarios.regimene_id')
                ->leftjoin('ciudades', 'ciudades.id', '=', 'usuarios.ciudade_id')
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
     * Crea un usuario en estado pendiente por verificación
     */
    public static function crearUsuario( $data ) {

	    $id = Usuario::insertGetId([
        'tipopersona_id' => $data['tipoPersona'],
        'regimene_id' => $data['regimen'],
        'nit' => $data['nit'],
        'tipodocumento_id' => $data['tipodocumento_id'],
        'digitoverificacion' => $data['digitoverificacion'],
        'razon_social' => $data['razon_social'],
        'primer_apellido' => $data['primer_apellido'],
        'segundo_apellido' => $data['segundo_apellido'],
        'primer_nombre' => $data['primer_nombre'],
        'segundo_nombre' => $data['segundo_nombre'],
        'email' => $data['email'],
        'password' => $data['password'],
        'ciudade_id' => $data['ciudade_id'],
        'direccion' => $data['direccion'],
        'telefono' => $data['telefono'],
        'celular' => $data['celular'],
        'nombre_contacto' => $data['nombreContacto'],
        'usuario_id' => $data['usuario_id'],
        'listaprecio' => $data['listaprecio'],
        'estado_id' => $data['estado_id'],
        'created_at' => $data['created_at']
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
          'created_at' => $data['created_at']
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

    /**
     * Obtiene la información del usuario por su id
     */
    public static function obtenerUsuarioPorId( $usuarioId ) {
      $data = Usuario::select()
              ->where('id', $usuarioId)
              ->get();
      return $data;      
    }

    /**
     * Obtiene la información de un usuario por medio de la identificacion
     */
    public static function obtenerUsuarioPorIdent( $nit ) {
      $data = Usuario::select()
              ->where('nit', $nit)
              ->get();
      return $data;
    }    

}