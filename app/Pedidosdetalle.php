<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedidosdetalle extends Model
{
    /**
     * Se obtiene la información de todos los detalles de un pedido específico registrado en la aplicacion
     */
    public static function obtenerDetallesPedido( $pedidoId ) {
		  $data = Pedidosdetalle::select( 'pedidos.id as id', 'pedidos.fechapedido', 'items.impto_incluido',
                                      'pedidos.url_guia', 'pedidosdetalles.id as detalleId', 'pedidosdetalles.item_id', 
                                      'items.descripcion as desc_item', 'items.referencia', 'items.codigo',
                                      'pedidosdetalles.cantidad', 'pedidosdetalles.vlr_item', 'pedidosdetalles.vlr_impuesto', 
                                      'usuarios.id as usuario_id', 'usuarios.primer_nombre', 'usuarios.segundo_nombre',
                                      'usuarios.primer_apellido', 'usuarios.segundo_apellido', 'usuarios.nit',
                                      'estadopedidos.id as estadoId', 'estadopedidos.descripcion as descEstado',
                                      'estadopedidos.orden as ordenpedido', 'estadopedidos.mostrar')
                                      
                ->join('pedidos', 'pedidos.id', '=', 'pedidosdetalles.pedido_id')
                ->join('usuarios', 'usuarios.id', '=', 'pedidos.usuario_id')
                ->join('estadopedidos', 'estadopedidos.id', '=', 'pedidos.estadopedido_id')
                ->join('items', 'items.id', '=', 'pedidosdetalles.item_id')
                ->where('pedidosdetalles.pedido_id', $pedidoId)
                ->get();
    	return $data;     	
    }

    /**
     * Guarda el detalle del pedido y retorna el id
     */
    public static function guardarDetallePedido( $data ) {
	    $id = Pedidosdetalle::insertGetId( $data );	          
      return $id;        
    }

    /**
     * Verifica la existencia de un producto en el pedido del usuario y lo actualiza si aplica
     */
    public static function validarActualizarPedido( $regId, $itemId, $cantItem ) {
      $pedDet = Pedidosdetalle::select()
                                ->where('pedido_id', $regId)
                                ->where('item_id', $itemId)
                                ->get();

      // Valida si existe registro del item en un pedido de un usuario especifico
      if(!empty($pedDet['0']->id)) {
        return $pedDet;
      }
      
      return null;
    }

    /**
     * Elimina un item de un pedido específico
     */
    public static function eliminarItemPedido( $idItem, $idPed ) {
      // obtiene la informacion del item
      $detalle = Pedidosdetalle::select()
                  ->where('pedidosdetalles.pedido_id', $idPed)
                  ->where('pedidosdetalles.item_id', $idItem)
                  ->get();

      // verifica si el item existe
      if(!empty($detalle['0']->id)) {
          $detalle['0']->delete();
          return true;
      }
      
      return false;      
    }

    /**
     * Actualizar cantidad de un registro específico
     */
    public static function actualizarCantidadItem( $itemId, $idPed, $cant ) {
      $pedDet = Pedidosdetalle::select()
                                ->where('pedido_id', $idPed)
                                ->where('item_id', $itemId)
                                ->get();

      // Valida si existe y actualiza la cantidad del registro
      if(!empty($pedDet['0']->id)) {
        $pedDet['0']->cantidad = $cant;
        $pedDet['0']->save();

        return true;
      }
      
      return false;      
    }

    /**
     * Obtiene un producto de un pedido específico al cual no se le haya
     * actualizado la información de cantidad solicitada y cantidad disponible,
     * si ya se actualizo, se pasa por alto, sino, se actualiza el registro
     */
    public static function obtenerProductoEnDetalle($pedidoId, $itemId, $cantPedida, $cantDisponible) {
      $resp = Pedidosdetalle::select()
                            ->where('pedido_id', '=', $pedidoId)
                            ->where('item_id', '=', $itemId)
                            ->where('cant_disponible', '=', null)
                            ->where('cant_pedida', '=', null)
                            ->get();
      
      if( !empty( $resp['0']->id ) ) {
        $resp['0']->cant_disponible = $cantDisponible;
        $resp['0']->cant_pedida = $cantPedida;
        $resp['0']->save();

        return true;
      }

      return false;

    }

    /**
     * obtiene la información del pedido registrado en cotools y datax
     */
    public static function obtenerPedidoWeb($userId, $pedidoId) {
      $data = Pedidosdetalle::select( 'pedidos.id', 'pedidos.updated_at', 'pedidos.fechapedido', 'pedidos.usuario_id',                                      
                                      Pedidosdetalle::raw('(pedidosdetalles.cantidad * pedidosdetalles.vlr_item) as total')
                              ) 
                      ->join('pedidos', 'pedidos.id', '=', 'pedidosdetalles.pedido_id')
                      ->where('pedidos.usuario_id', '=', $userId)
                      ->where('pedidos.id', '=', $pedidoId)
                      ->get();
      return $data;
    }    
   
}