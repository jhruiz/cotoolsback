<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{

  /**
   * Se obtiene la información los grupos registrados en la base de datos
   */
  public static function obtenerGrupos( ) {
    $data = Grupo::select()
            ->where('grupos.clasegrupo_id', '=', '1')
            ->get();
    return $data;      	
  }

  /**
   * Crea un grupo
   */
  public static function crearGrupo( $data ) {
    $id = Grupo::insertGetId([
      'grupo' => $data['grupo'],
      'subgrupo' => $data['subgrupo'],
      'descripcion' => $data['descripcion'],
      'clasegrupo_id' => $data['clasegrupo_id'],
      'created_at' => $data['created_at']
    ]);	 
    
    return $id;     
  }

  /**
   * Obtiene un grupo específico por descripción
   */
  public static function obtenerGrupoPorDesc( $descripcion ) {
    $data = Grupo::select()
            ->where('descripcion', $descripcion)
            ->get();
    return $data;    
  }

  /**
   * Actualiza la información de un grupo específico
   */
  public static function actualizarGrupo( $grupo, $subgrupo, $claseId, $grupoId ) {
    // obtiene la información de un grupo
    $grupo = Grupo::find($grupoId);
    
    // valida que el departamento exista
    if( !empty( $grupo ) ) {
        $grupo->grupo = $grupo;
        $grupo->subgrupo = $subgrupo;
        $grupo->clasegrupo_id = $claseId;
        $grupo->save();

        return true;
    }

    return false;      
  }

}
