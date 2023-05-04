<?php

namespace App\Http\Controllers;

use App\Configuraciondato;

use App\Usuario;
use App\Tipopersona;
use App\Tipodocumento;
use App\Regimene;
use App\Perfile;
use App\PerfilesUsuario;
use App\Ciudade;
use App\Mail\usuarioCreado;
use App\Mail\usuarioActivado;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;

class UsuariosController extends Controller
{

    public function enviarCorreo() {
        $email = 'jaiber.ruiz@miggo.com.co';
        $resp = Mail::to($email)->send(new usuarioActivado);
        dd('Success! Email has been sent successfully.');
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
    public function crearUsuario(Request $request) 
    {

        $nit = $request['identificacion'];
        $email = $request['email'];
        $ciudad = $request['ciudad'];
        $direccion = $request['direccion'];        
        $celular = $request['celular'];
        $telefono = !empty($request['telefono']) ? $request['telefono'] : '';
        $tipoPersona = !empty($request['tipoPersona']) ? $request['tipoPersona'] : '1';
        $perfiles = !empty($request['perfiles']) ? $request['perfiles'] : null;

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $nit ) && !empty( $email ) && !empty( $ciudad ) && !empty( $direccion ) && !empty( $celular ) ) 
            {

                // Verifica si el usuario ya existe en la base de datos por medio de su correo
                if( !$this->verificarUsuarioExiste( $email ) ) {

                    // Obtiene el nombre o la razon social del cliente
                    $nombres = $this->obtenerNombresCliente($tipoPersona, trim($request['nombres']), trim($request['apellidos']), trim($request['razonSocial']));

                    // Se obtiene el id del regimen
                    $regimen = isset($request['regimen']) ? $request['regimen'] : 'IV';
                    $regimenId = Regimene::obtenerRegimenPorCodigo( $regimen )['0']->id;

                    // Se obtiene el id del tipo de documento
                    $tipoDoc = isset($request['tipoDocumento']) ? $request['tipoDocumento'] : 'C';
                    $tipoDocId = Tipodocumento::obtenerTipodocumentoPorCodigo( $tipoDoc )['0']->id;

                    // Se obtiene el vendedor
                    $vendedor = isset($request['vendedor']) ? $request['vendedor'] : '114048654';
                    $vendedorId = Usuario::obtenerUsuarioPorIdent( $vendedor )['0']->id;

                    // Se genera y obtiene la contraseña
                    $contrasenia = $this->generarContrasenia( $nit );

                    if( !empty( $contrasenia ) ){
                        
                        $data = array(
                            'tipoPersona' => $tipoPersona,
                            'regimen' => $regimenId,
                            'nit' => $nit,
                            'tipodocumento_id' => $tipoDocId,
                            'digitoverificacion' => isset($request['digVerificacion']) ? $request['digVerificacion'] : '',
                            'razon_social' => $nombres['razon_social'],
                            'primer_apellido' => $nombres['primer_apellido'],
                            'segundo_apellido' => $nombres['segundo_apellido'],
                            'primer_nombre' => $nombres['primer_nombre'],
                            'segundo_nombre' => $nombres['segundo_nombre'],
                            'email' => $email,
                            'password' => $contrasenia,
                            'ciudade_id' => $ciudad,
                            'direccion' => $direccion,
                            'telefono' => $telefono,
                            'celular' => $celular,
                            'nombreContacto' => !empty($request['nombreContacto']) ? $request['nombreContacto'] : '',
                            'usuario_id' => $vendedorId,
                            'listaprecio' => !empty($request['listaPrecios']) ? $request['listaPrecios'] : '1',
                            'estado_id' => '2',
                            'created_at' => date('Y-m-d H:i:s')
                        );

                        // Crea el usuario
                        $id = Usuario::crearUsuario( $data );

                        if( $id ) {

                            // Si el arreglo de perfiles es diferente de vacio, crea la relacion perfil y usuario                            
                            PerfilesUsuario::crearPerfilUsuario($perfiles['0'], $id, $data['created_at']);

                            // Se envía correo por la creación del cliente
                            $this->enviarCorreoCreacion($data);

                            $this->enviarCorreoActivacion($email);

                            $resp['estado'] = true;
                            $resp['data'] = $id;                            
                        } else {
                            $resp['mensaje'] = 'No fue posible crear al usuario';
                        }

                    } else {
                        $resp['mensaje'] = 'No fue posible codificar la contraseña';
                    }

                } else {
                    $resp['mensaje'] = 'El usuario ' . $email . ' ya se encuentra registrado en nuestra base de datos';
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
     * Crea un usuario en estado pendiente por verificar
     */
    public function crearUsuarioSinc(Request $request) 
    {
        $nit = $request['identificacion'];
        $email = $request['email'];
        $ciudad = $request['ciudad'];
        $direccion = $request['direccion'];        
        $celular = $request['celular'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $nit ) && !empty( $email ) && !empty( $ciudad ) && !empty( $direccion ) && !empty( $celular ) ) 
            {

                // Verifica si el usuario ya existe en la base de datos por medio de su correo
                if( !$this->verificarUsuarioExiste( $email ) ) {

                    // Obtiene el nombre o la razon social del cliente
                    // $nombres = [];
                    // if( isset( $request['tipoPersona'] ) ) {
                    //     $nombres = $this->obtenerNombresCliente($request['tipoPersona'], $request['nombres'], $request['apellidos'], $request['razonSocial']);
                    // } else {
                    //     $nombres['razon_social'] = !empty($request['razonSocial']) ? $request['razonSocial'] : '',
                    //     $nombres['primer_apellido'] = !empty($request['primerApellido'] ? $request['primerApellido'] : ''),
                    //     $nombres['segundo_apellido'] = !empty($request['segundoApellido']) ? $request['segundoApellido'] : '',
                    //     $nombres['primer_nombre'] = !empty($request['primerNombre']) ? $request['primerNombre'] : '',
                    //     $nombres['segundo_nombre'] = !empty($request['segundoNombre']) ? $request['segundoNombre'] : '',                        
                    // }

                    // Se obtiene el id del tipo de persona
                    $tipoPersona = !empty($request['tipoPersona']) ? $request['tipoPersona'] : 'N';
                    $tipoPerId = Tipopersona::obtenerTipoPersonaPorCodigo( $tipoPersona )['0']->id;

                    // Se obtiene el id del regimen
                    $regimen = !empty($request['regimen']) ? $request['regimen'] : '2';
                    $regimenId = Regimene::obtenerRegimenPorCodigo( $regimen )['0']->id;

                    // Se obtiene el id del tipo de documento
                    $tipoDoc = !empty($request['tipoDocumento']) ? $request['tipoDocumento'] : 'C';
                    $tipoDocId = Tipodocumento::obtenerTipodocumentoPorCodigo( $tipoDoc )['0']->id;

                    // Se obtiene el id de la ciudad
                    $ciudadId = Ciudade::obtenerCiudadPorDesc( $ciudad )['0']->id;

                    // Se obtiene el vendedor
                    $vendedor = !empty($request['vendedor']) ? $request['vendedor'] : '';
                    $vendedorId = Usuario::obtenerUsuarioPorIdent( $vendedor );

                    // Se genera y obtiene la contraseña
                    $contrasenia = $this->generarContrasenia( $nit );

                    if( !empty( $contrasenia ) ){
                        
                        $data = array(
                            'tipoPersona' => $tipoPerId,
                            'regimen' => $regimenId,
                            'nit' => $nit,
                            'tipodocumento_id' => $tipoDocId,
                            'digitoverficacion' => !empty($request['digVerificacion']) ? $request['digVerificacion'] : '',
                            'razon_social' => !empty($request['razonSocial']) ? $request['razonSocial'] : '',
                            'primer_apellido' => !empty($request['primerApellido'] ? $request['primerApellido'] : ''),
                            'segundo_apellido' => !empty($request['segundoApellido']) ? $request['segundoApellido'] : '',
                            'primer_nombre' => !empty($request['primerNombre']) ? $request['primerNombre'] : '',
                            'segundo_nombre' => !empty($request['segundoNombre']) ? $request['segundoNombre'] : '',
                            'email' => $email,
                            'password' => $contrasenia,
                            'ciudade_id' => $ciudadId,
                            'direccion' => $direccion,
                            'telefono' => !empty($request['telefono']) ? $request['telefono'] : '',
                            'celular' => $celular,
                            'nombreContacto' => !empty($request['nombreContacto']) ? $request['nombreContacto'] : '',
                            'usuario_id' => $vendedorId,
                            'listaprecio' => !empty($request['listaPrecios']) ? $request['listaPrecios'] : '',
                            'estado_id' => '2',
                            'created_at' => date('Y-m-d H:i:s')
                        );

                        // Crea el usuario
                        $id = Usuario::crearUsuario( $data );

                        if( $id ) {

                            // Se obtienen los perfiles creados
                            $perfiles = Perfile::obtenerPerfiles();

                            // Si el arreglo de perfiles es diferente de vacio, crea la relacion perfil y usuario                            
                            if(!empty($perfiles)){
                                foreach($perfiles as $key => $val) {
                                    PerfilesUsuario::crearPerfilUsuario($key, $id, $data['created_at']);
                                }
                            }

                            $resp['estado'] = true;
                            $resp['data'] = $id;                            
                        } else {
                            $resp['mensaje'] = 'No fue posible crear al usuario';
                        }

                    } else {
                        $resp['mensaje'] = 'No fue posible codificar la contraseña';
                    }

                } else {
                    $resp['mensaje'] = 'El usuario ' . $email . ' ya se encuentra registrado en nuestra base de datos';
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
        $email = 'jaiber.ruiz@hotmail.com'; //$request['email'];     

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // Verifica que se ingresara el id del usuario
            if( !empty( $usuarioId ) && !empty($estadoId) ){

                // Registra los nuevos perfiles para el usuario
                if( !empty( $perfiles ) ) {

                    // Elimina los perfiles relacionados al usuario
                    PerfilesUsuario::eliminarPerfilesUsuario( $usuarioId );                    

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
     * Realiza el login del usuario
     */
    public function loginUsuario(Request $request) {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        $usuarioId = $request['user'];
        $contrasenia = $request['password'];

        try{

            if( !empty($usuarioId) && !empty($contrasenia) ) {

                // Se obtiene la información del usuario
                $usuario = Usuario::obtenerUsuarioPorEmail( $usuarioId );

                // Verifica si el usuario existe
                if( !empty($usuario['0']->id) ) {

                    // Se obtiene la ciudad donde vive el usuario
                    $ciudad = Ciudade::obtenerCiudadPorId( $usuario['0']->ciudade_id );                    

                    // Verifica si las contraseñas son iguales
                    if( password_verify($contrasenia, $usuario['0']->password) ) {
                        $resp['estado'] = true;
                        $resp['data'] = $usuario;
                        $resp['datac'] = $ciudad;

                        // Setea la posición password del array del usuario
                        $usuario['0']->password = '';
                        
                        // Se agrega la variable user a la sesion
                        $request->session()->put('user', $usuario['0']);
                        
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
     * 
     */
    public function obtenerNombresCliente($tipoPersona, $nombres, $apellidos, $razonSocial) {
        $arrNombresFinal = array(
            'primer_nombre' => '',
            'segundo_nombre' => '',
            'primer_apellido' => '',
            'segundo_apellido' => '',
            'razon_social' => '',
        );

        // Valida si el tipo de persona es natura(1) o persona juridica(2)
        if( $tipoPersona == '1' ){

            // Valida si el cliente ingresó dos nombres
            if( strpos( $nombres, ' ') ) {
                $arrNombres = explode(' ', $nombres);
                $arrNombresFinal['primer_nombre'] = $arrNombres['0'];
                $arrNombresFinal['segundo_nombre'] = $arrNombres['1'];
            } else {
                $arrNombresFinal['primer_nombre'] = $nombres;
            }

            // Valida si el cliente ingresó dos apellidos
            if( strpos( $apellidos, ' ') ) {
                $arrApellidos = explode(' ', $apellidos);
                $arrNombresFinal['primer_apellido'] = $arrApellidos['0'];
                $arrNombresFinal['segundo_apellido'] = $arrApellidos['1'];
            } else {
                $arrNombresFinal['primer_apellido'] = $apellidos;
            }

        } else {
            $arrNombresFinal['razon_social'] = $razonSocial;
        }
        return $arrNombresFinal;
    }

}