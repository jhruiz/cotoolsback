<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedidosdetalle extends Model
{
    /**
     * Se obtiene la información de todos los detalles de un pedido específico registrado en la aplicacion
     */
    public static function obtenerDetallesPedido( $pedidoId ) {
		  $data = Pedidosdetalle::select( 'pedidos.id as pedidoId', 'pedidos.cod_benf', 'pedidos.nro_pdweb',
                                      'pedidos.estadopago', 'pedidos.created as fechaPedido', 'pedidos.url_guia',                                       
                                      'pedidosdetalles.id as detalleId', 'pedidosdetalles.cod_item', 'pedidosdetalles.descripcion',
                                      'pedidosdetalles.cantidad', 'pedidosdetalles.precioventaunit', 'pedidosdetalles.vlriva', 
                                      'pedidosdetalles.tasaiva', 'usuarios.id as usuarioId', 'usuarios.nombre', 
                                      'usuarios.identificacion', 'estadospedidos.id as estadoId', 'estadospedidos.descripcion as descEstado')
                                      
                ->join('pedidos', 'pedidos.id', '=', 'pedidosdetalles.pedido_id')
                ->join('usuarios', 'usuarios.id', '=', 'pedidos.usuario_id')
                ->join('estadospedidos', 'estadospedidos.id', '=', 'pedidos.estadospedido_id')
                ->where('pedidosdetalles.pedido_id', $pedidoId)
                ->get();
    	return $data;     	
    }

    /**
     * Guarda el detalle del pedido y retorna el id
     */
    public static function guardarDetallePedido( $data ) {
	    $id = Pedidosdetalle::insertGetId([
            'pedido_id' => $data['pedido_id'],
            'cod_item' => $data['codigo_item'],   
            'descripcion' => $data['desc'],   
            'cantidad' => $data['cantidad'],
            'precioventaunit' => $data['precioventaunit'],
            'estado' => $data['estado'], 
            'vlriva' => $data['vlriva'],
            'tasaIva' => $data['tasaiva'],
            'created' => $data['created']
          ]);	
          
      return $id;        
    }

    /**
     * Verifica la existencia de un producto en el pedido del usuario y lo actualiza si aplica
     */
    public static function validarActualizarPedido( $regId, $codItem, $cantItem ) {
      $pedDet = Pedidosdetalle::select()
                                ->where('pedido_id', $regId)
                                ->where('cod_item', $codItem)
                                ->get();

      // Valida si existe registro del item en un pedido de un usuario especifico
      if(!empty($pedDet['0']->id)) {

        // si el producto esta inactivo por pedir borrarlo, actualiza la cantidad, sino, suma la cantidad adicional
        // $cant = $pedDet['0']->estado == '1' ? $pedDet['0']->cantidad + $cantItem : $cantItem;

        // $pedDet['0']->cantidad = $cant;
        // $pedDet['0']->estado = '1';
        // $pedDet['0']->save();

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
                  ->where('pedidosdetalles.cod_item', $idItem)
                  ->get();

      // verifica si el item existe
      if(!empty($detalle['0']->id)) {
          $detalle['0']->estado = '0';
          $detalle['0']->save();
          return true;
      }
      
      return false;      
    }

    /**
     * Actualizar cantidad de un registro específico
     */
    public static function actualizarCantidadItem( $codItem, $idPed, $cant ) {
      $pedDet = Pedidosdetalle::select()
                                ->where('pedido_id', $idPed)
                                ->where('cod_item', $codItem)
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
    public static function obtenerProductoEnDetalle($pedidoId, $cod_item, $cantPedida, $cantDisponible) {
      $resp = Pedidosdetalle::select()
                            ->where('pedido_id', '=',$pedidoId)
                            ->where('cod_item', '=',$cod_item)
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
    public static function obtenerPedidoWeb($userId, $pdWeb) {
      $data = Pedidosdetalle::select( 'pedidos.nro_pdweb', 'pedidos.updated_at',
                                      Pedidosdetalle::raw('(pedidosdetalles.cantidad * pedidosdetalles.precioventaunit) as total')
                              ) 
                      ->join('pedidos', 'pedidos.id', '=', 'pedidosdetalles.pedido_id')
                      ->where('pedidos.usuario_id', '=', $userId)
                      ->where('pedidos.nro_pdweb', '=', $pdWeb)
                      ->where('pedidosdetalles.estado', '=', '1')
                      ->get();
      return $data;
    }    
   
}