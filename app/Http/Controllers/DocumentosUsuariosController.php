<?php

namespace App\Http\Controllers;

use App\DocumentosUsuario;
use Illuminate\Http\Request;

class DocumentosUsuariosController extends Controller
{
    
    /**
     * Retorna la información de todos los documentos de usuarios registrados en la base de datos
     */
    public function obtenerDocumentosUsuario(Request $request)
    {   
        $usuarioId = $request['usuarioId'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $usuarioId ) ) {
                // se obtienen los documentos de los usuarios
                $documentosUsuario = DocumentosUsuario::obtenerDocumentosUsuario( $usuarioId ); 
                
                // valida si se econtraron registros
                if( !empty( $documentosUsuario ) ) {
                    $resp['estado'] = true;
                    $resp['data'] = $documentosUsuario;
                } else {
                    $resp['mensaje'] = 'No se encontraron los documentos del usuario';
                }                
            } else {
                $resp['mensaje'] = 'Debe seleccionar un cliente';
            }


        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Guarda la información del documento relacionado al usuario
     */
    public function crearDocumentosUsuario(Request $request) {

        $usuarioId = $request['usuarioId'];
        $documentos = $request['documentos'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // valida que se hayan enviado los datos del documento y del usuario
            if(!empty($usuarioId) && count($documentos) > 0) {

                foreach($documentos as $key => $val) {
                    $data = array(
                        'documento_id' => $val['id'],
                        'url' => $val['nombre'],
                        'usuario_id' => $usuarioId,
                        'created' => date('Y-m-d H:i:s')
                    );
    
                    // Guarda la informacion del documento y el usuario
                    $regId = DocumentosUsuario::guardarDocumentosUsuario( $data );
    
                    // valida si el registro fue almacenado de forma correcta
                    if( !empty( $regId ) ) {
                        $resp['estado'] = true;
                        $resp['data'] = $regId;
                    } else {
                        $mensaje = 'Ups! Algo salio mal en la carga del archivo.';
                    }
                }
     
            } else {
                $resp['mensaje'] = 'Debe ingresar un documento y un usuario.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;
    }

    /**
     * Actualiza el estado de un documento cuando este ya ha sido revisado o verificado
     */
    public function verficarDocumento( Request $request ) {

        $documentoUsuarioId = $request['documentoUsuarioId'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // Valida que se hay enviado la informacion de un documento de un usuario para realizar el registro
            if( !empty( $documentoUsuarioId ) ) {
                $rp = DocumentosUsuario::verificarDocumentoUsuario($documentoUsuarioId);

                // valida si fue posible realizar la actualización del documento para el usuario
                if( $rp ) {
                    $resp['estado'] = true;
                } else {
                    $resp['mensaje'] = 'El documento seleccionado no fue encontrado en los registros';
                }
                
            } else {
                $resp['mensaje'] = 'Debe seleccionar un documento';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;
    }

}