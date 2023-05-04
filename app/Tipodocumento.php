<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipodocumento extends Model
{
    /**
     * Se obtienen los tipos de documentos
     */
    public static function obtenerTiposdocumentos( ) {
		$data = Tipodocumento::select()
                ->get();
    	return $data;  
    }

    /**
     * Crear un nuevo tipo de documento
     */
    public static function crearTipodocumento( $data ) {
        $id = Tipodocumento::insertGetId([
            'codigo' => $data['codigo'],
            'descripcion' => $data['descripcion'],
            'created_at' => $data['created_at']
          ]);	 
          
        return $id;        
    }

    /**
     * Se obtiene un tipo documento por código
     */
    public static function obtenerTipodocumentoPorCodigo( $codigo ) {
        $data = Tipodocumento::select()
                ->where('codigo', $codigo)
                ->get();
        return $data;         
    }

    /**
     * Se actualiza la descripcion de un tipo de documento específico
     */
    public static function actualizarTipodocumento( $tipDocId, $descripcion ) {
        // obtiene la información del tipo de documento
        $tipDoc = Tipodocumento::find( $tipDocId );
        
        // valida que el regimen exista
        if( !empty( $tipDoc ) ) {
            $tipDoc->descripcion = $descripcion;
            $tipDoc->save();

            return true;
        }

        return false;         
    }
}