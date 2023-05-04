<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{

  /**
   * Se obtiene la información los saldos registrados en la base de datos
   */
  public static function obtenerSaldos( $item_id ) {
    $data = Saldo::select()
            ->where('item_id', $item_id)
            ->get();
    return $data;      	
  }

  /**
   * Crea el saldo de un producto en una bodega específica
   */
  public static function crearSaldo( $data ) {
    $id = Saldo::insertGetId([
        'item_id' => $data['item_id'],
        'codigobodega' => $data['codigobodega'],
        'saldoactual' => $data['saldoactual'],
        'saldopedidos' => $data['saldopedidos'],
        'created_at' => $data['created_at']
      ]);	 
      
    return $id; 
  }

  /**
   * Actualiza la información del saldo de un item en una bodega específica
   */
  public static function actualizarSaldo( $itemId, $codigobodega, $saldoactual, $saldopedidos ) {
        // obtiene la información del saldo de un item en una bodega específica
        $saldo = Saldo::select( )
                    ->where('item_id', $item_id)
                    ->where('codigobodega', $codigobodega)
                    ->get();
        
        // valida que el saldo exista
        if( !empty( $saldo ) ) {
            $saldo->saldoactual = $saldoactual;
            $saldo->saldopedidos = $saldopedidos;
            $impuesto->save();

            return true;
        }

        return false;       
  }

}