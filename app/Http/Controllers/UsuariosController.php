<?php

namespace App\Http\Controllers;

use App\Usuario;
use App\PerfilesUsuario;
use App\Configuraciondato;
use App\Mail\usuarioCreado;
use App\Mail\usuarioActivado;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;

class UsuariosController extends Controller
{

    public function enviarCorreo() {
        $email = 'jaiber.ruiz@hotmail.com';
        Mail::to($email)->send(new usuarioActivado);
    }

    /**
     * Envia correo de creación de cliente
     */
    public function enviarCorreoCreacion($data) {
        
        //obtiene la información de los usuarios a los que debe enviarle el correo de creacion de tercero
        $nombre = 'crearusr';
        $correos = Configuraciondato::obtenerConfiguracion($nombre)['0']->valor;

        //se obtienen los email configurados para enviar el correo (destinatarios)
        $arrMails = explode(",", $correos);

        //se envian los correos configurados
        foreach($arrMails as $m) {
            Mail::to($m)->send(new usuarioCreado((object) $data));
        }
    }

    /**
     * Envía el correo al cliente indicandole que su usuario fue activado
     */
    public function enviarCorreoActivacion($email) {
        //se envian los correo de activación al cliente
        Mail::to($email)->send(new usuarioActivado);
    }
    
    /**
     * Retorna la información de todos los usuarios registrados en la base de datos
     */
    public function obtenerUsuarios(Request $request)
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen los usuarios
            $usuarios = Usuario::obtenerUsuarios();           

            // valida si se econtraron registros
            if( !empty( $usuarios ) ) {
                $resp['estado'] = true;
                $resp['data'] = $usuarios;
            } else {
                $resp['mensaje'] = 'No se encontraron los usuarios';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Retorna la información de un usuario especifico registrado en la aplicacion
     */
    public function obtenerUsuario(Request $request)
    {

        $id = $request['usuarioId'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $id ) ){
                
                // se obtienen los usuarios
                $usuarios = Usuario::obtenerUsuario($id);  

                // valida si se econtraron registros
                if( !empty( $usuarios ) ) {
                    $resp['estado'] = true;
                    $resp['data'] = $usuarios;
                } else {
                    $resp['mensaje'] = 'No se encontraron los usuarios';
                }

            } else {
                $resp['mensaje'] = 'Debe ingresar un id';
            }


        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Crea un usuario en estado pendiente por verificar
     */
    public function crearUsuario(Request $request) {
        
        $nombre = $request['nombre'];
        $identificacion = $request['identificacion'];
        $email = $request['email'];        
        $perfiles = $request['perfiles'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $nombre ) && !empty( $identificacion ) && !empty( $email ) ) {

                // Verifica si el usuario ya existe en la base de datos por medio de su correo
                if( !$this->verificarUsuarioExiste( $email ) ) {

                    $contrasenia = $this->generarContrasenia( $identificacion );

                    if( !empty( $contrasenia ) ){
                        
                        $data = array(
                            'nombre' => $nombre,
                            'identificacion' => $identificacion,
                            'email' => $email,
                            'contrasenia' => $contrasenia,
                            'created' => date('Y-m-d H:i:s')
                        );

                        // Crea el usuario
                        $id = Usuario::crearUsuario( $data );

                        if( $id ) {

                            // Si el arreglo de perfiles es diferente de vacio, crea la relacion perfil y usuario
                            if(!empty($perfiles)){
                                foreach($perfiles as $key => $val) {
                                    PerfilesUsuario::crearPerfilUsuario($val, $id, $data['created']);
                                }
                            }

                            // Envía correo de creación de cliente
                            $this->enviarCorreoCreacion($data);

                            $resp['estado'] = true;
                            $resp['data'] = $id;                            
                        } else {
                            $resp['mensaje'] = 'No fue posible crear al usuario';
                        }

                    } else {
                        $resp['mensaje'] = 'No fue posible codificar la contraseña';
                    }

                } else {
                    $resp['mensaje'] = 'El usuario ' . $nombre . ' ya se encuentra registrado en nuestra base de datos';
                }

            } else {
                $resp['mensaje'] = 'La información para creación del usuario no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;
    }

    /**
     * Genera la contraseña a partir de la identificación sin el código de verificación
     */
    public function generarContrasenia( $identificacion ) {

        $resp = '';

        // Verifica que se reciba la identificación
        if( !empty( $identificacion ) ) {

            // Verifica que exista el guion para el codigo de verificación
            if( strpos($identificacion, '-') !== false) {

                // Separa por guion la información
                $arrIdent = explode( '-', $identificacion );

                // Encripta la identificación sin codigo de verificación
                $resp = password_hash(trim($arrIdent['1']), PASSWORD_BCRYPT);

            } else {

                // Encripta la identificación
                $resp = password_hash($identificacion, PASSWORD_BCRYPT);
            }
        }

        return $resp;

    }

    /**
     * Valida si un usuario existe por medio de su email
     */
    public function verificarUsuarioExiste( $email ) {

        $resp = false;

        if( !empty( $email ) ) {
            $usuario = Usuario::obtenerUsuarioPorEmail( $email );

            // valida si se obtiene un usuario
            if( !empty($usuario['0']->id) ){

                $resp = true;
            }
        }

        return $resp;
    }

    /**
     * Actualiza el estado del usuario a verificado o activado
     */
    public function actualizarUsuario( Request $request ) {

        $usuarioId = $request['id'];
        $estadoId = $request['estado'];
        $perfiles = $request['perfiles'];
        $email = $request['email'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // Verifica que se ingresara el id del usuario
            if( !empty( $usuarioId ) && !empty($estadoId) ){

                // Elimina los perfiles relacionados al usuario
                PerfilesUsuario::eliminarPerfilesUsuario( $usuarioId );

                // Registra los nuevos perfiles para el usuario
                if( !empty( $perfiles ) ) {
                    foreach( $perfiles as $key => $val ) {
                        $created = date('Y-m-d H:i:s');
                        PerfilesUsuario::crearPerfilUsuario($val, $usuarioId, $created);
                    }
                }
                
                // Actualizar estado del usuario
                $rp = Usuario::actualizarUsuario( $usuarioId, $estadoId );

                // valida si fue posible realizar la actualización del documento para el usuario
                if( $rp ) {

                    // valida que el nuevo estado sea activo
                    if($estadoId == '2'){
                        // envía correo de activación de usuario
                        $this->enviarCorreoActivacion($email);
                    }

                    $resp['estado'] = true;
                } else {
                    $resp['mensaje'] = 'El usuario seleccionado no fue encontrado en los registros';
                }                

            } else {
                $resp['mensaje'] = 'Debe ingresar un usuario y un estado';
            }


        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }

    /**
     * Obtiene la información del tercero desde datax
     */
    public function obtenerDatosTerceroDatax($identificacion, $email) {

        // se crea un cliente guzzle para obtener por api la información de los clientes de datax
        try{
            $urlDatax = Configuraciondato::obtenerConfiguracion('urldatax');

            $client = new Client();
            $response = $client->request('GET', $urlDatax['0']->valor . 'get-client/' . $identificacion . '/' . $email);
            
            if($response->getStatusCode() == '200') {
                $content = (string) $response->getBody()->getContents();
                $usuarios = json_decode($content);
                
                if( !empty( $usuarios->data ) ) {
                    $infoClient = $usuarios->data;
                } else {
                    $infoClient = null;
                }
                
            } else {
                $infoClient = null;
            }
    
            return $infoClient;
        }catch(Throwable $e) {
            return null;
        }

    }

    /**
     * Realiza el login del usuario
     */
    public function loginUsuario(Request $request) {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        $usuarioId = $request['user'];
        $contrasenia = $request['password'];

        try{

            if( !empty($usuarioId) && !empty($contrasenia) ) {

                // Se obtiene la información del usuario
                $usuario = Usuario::obtenerUsuarioPorUsername( $usuarioId );

                // Verifica si el usuario existe
                if( !empty($usuario['0']->id) ) {

                    // Verifica si las contraseñas son iguales
                    if( password_verify($contrasenia, $usuario['0']->password) ) {
                        $resp['estado'] = true;
                        $resp['data'] = $usuario;

                        // Setea la posición password del array del usuario
                        $usuario['0']->password = '';
                        
                        // Se agrega la variable user a la sesion
                        $request->session()->put('user', $usuario['0']);
                        
                        // Obtiene la información del tercero registrado en datax
                        $resp['dataDatax'] = $this->obtenerDatosTerceroDatax($usuario['0']->identificacion, $usuario['0']->email);
                    } else {
                        $resp['mensaje'] = 'El usuario y/o la contraseña no son correctos';
                    }

                } else {
                    $resp['mensaje'] = 'Los datos de acceso no son correctos.';
                }
    
            } else {
                $resp['mensaje'] = 'Debe ingresar un usuario y contraseña';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Obtiene los usuarios registrados en la base de datos de Datax
     */
    public function obtenerUsuariosDatax() {    

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );
        
        $urlDatax = Configuraciondato::obtenerConfiguracion('urldatax');

        $client = new Client(); 

        $response = $client->request('GET', $urlDatax['0']->valor . 'get-clients');
        
        if($response->getStatusCode() == '200') {
            $content = (string) $response->getBody()->getContents();
            $usuarios = json_decode($content); 

            if($this->sincronizarUsuarios( $usuarios->data )) {
                $resp['estado'] = true;                
            } else {
                $resp['mensaje'] = 'No fue posible realizar la sincronización de los usuarios.';
            }
            
        } else {
            $resp['mensaje'] = 'No fue posible obtener resultados de Datax.';
        }
        
        return $resp;
    }

    /**
     * Sincroniza los usuarios obtenidos de datax en la bd de configuracion y control de cotools
     */
    public function sincronizarUsuarios($usuarios) {

        if(!empty($usuarios)) {

            foreach($usuarios as $usuario) {

                $resp = true;
                
                // Si no tiene email, el usuario no será sincronizado
                if(empty($usuario->email_benf) || empty($usuario->nit_benf) || empty($usuario->nom_benf)) { 
                    $resp = false;
                    continue; 
                }
    
                try {
    
                    // Verifica si el usuario ya existe en la base de datos por medio de su correo
                    if( !$this->verificarUsuarioExiste( $usuario->email_benf ) ) {
    
                        $contrasenia = $this->generarContrasenia( $usuario->nit_benf );
                            
                        $data = array(
                            'nombre' => $usuario->nom_benf,
                            'identificacion' => $usuario->nit_benf,
                            'email' => $usuario->email_benf,
                            'contrasenia' => $contrasenia,
                            'created' => date('Y-m-d H:i:s')
                        );
    
                        // Sincroniza el usuario
                        $usuarioId = Usuario::sincronizarUsuario( $data );

                        if(!empty($usuarioId)) { 
                            PerfilesUsuario::crearPerfilUsuario(4, $usuarioId, $data['created']);
                        }else {
                            $resp = false;
                        }
    
                    }
        
                } catch(Throwable $e) {
                    return false;
                }
            }

            return $resp;
        }     
        
        return false;
    }

}