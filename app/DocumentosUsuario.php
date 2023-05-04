<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentosUsuario extends Model
{
    /**
     * Se obtiene la información de todos los documentos de un usuario registrados en la aplicacion
     */
    public static function obtenerDocumentosUsuario( $usuarioId ) {
      $data = DocumentosUsuario::select('documentos_usuarios.id', 'documentos_usuarios.documento_id', 'documentos_usuarios.url', 
                                        'documentos_usuarios.verificado', 'documentos.descripcion')
              ->join('documentos', 'documentos.id', '=', 'documentos_usuarios.documento_id')
              ->where('usuario_id', $usuarioId)
              ->get();
      return $data;      	
    }

    /**
     * Guarda el documento relacionado al usuario
     */
    public static function guardarDocumentosUsuario( $data ) {
	    $id = DocumentosUsuario::insertGetId([
        'usuario_id' => $data['usuario_id'],
        'documento_id' => $data['documento_id'],        
        'url' => $data['url'],        
        'verificado' => 0,
        'created_at' => $data['created_at']
      ]);	
      
      return $id;
    }

    /**
     * Actualiza el campo verificado del documento, indicando que ya se realizó la respectiva revisión por parte de un usuario
     */
    public static function verificarDocumentoUsuario( $documentoUsuarioId ) {

      // obtiene la informacion del documento a verificar
      $docUser = DocumentosUsuario::find($documentoUsuarioId);
      
      // valida que el documento exista
      if( !empty( $docUser ) ) {
        $docUser->verificado = 1;
        $docUser->save();

        return true;
      }

      return false;

    }
} 
