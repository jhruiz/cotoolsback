<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    /**
     * Se obtiene la información de todos los pedidos registrados en la aplicacion
     */
    public static function obtenerPedidos( ) {
		  $data = Pedido::select( 'pedidos.id', 'pedidos.created_at', 'usuarios.primer_nombre', 
                              'usuarios.segundo_nombre', 'usuarios.primer_apellido', 'usuarios.segundo_apellido',
                              'usuarios.nit', 'usuarios.email', 'estadopedidos.descripcion',
                              'pedidos.usuario_id')
                      ->join('usuarios', 'usuarios.id', '=', 'pedidos.usuario_id')
                      ->join('estadopedidos', 'estadopedidos.id', '=', 'pedidos.estadopedido_id')
                      ->where('pedidos.carrito', '=', 0)
                      // ->groupBy('pedidos.id', 'pedidos.created_at',
                      //           'usuarios.nombre', 'usuarios.nit', 'usuarios.email', 'estadopedidos.descripcion')  
                      ->get();
    	return $data;     	
    }

    /**
     * Obtiene los pedidos realizados por un cliente especifico
     */
    public static function obtenerPedidosCliente($userId) {
      $data = Pedido::select('pedidos.id', 'pedidos.usuario_id', 'pedidos.fechapedido', 'pedidos.updated_at', 'estadopedidos.descripcion')
                    ->join('estadopedidos', 'estadopedidos.id', '=', 'pedidos.estadopedido_id')
                    ->where('pedidos.usuario_id', '=', $userId)
                    ->where('pedidos.carrito', '=', 0)
                    ->get();

      return $data;
    }

    /**
     * Guarda el pedido y retorna el id
     */
    public static function guardarPedido( $data ) {
	    $id = Pedido::insertGetId($data);	    
      return $id;        
    }

    /**
     * Actualiza el pedido a pagado en estado pago
     */
    public static function actualizarEstadoPago( $id ) {
        // obtiene la informacion del pedido que se desea actualizar
        $pedido = Pedido::find( $id );
        
        // valida que el pedido exista
        if( !empty( $pedido ) ) {
            $pedido->estadopago = 1;
            $pedido->save();

            return true;
        }

        return false;        
    }

    /**
     * Actualiza el estado del pedido
     */
    public static function actualizarEstadoPedido( $pedidoId, $estadoPedId ) {
        // obtiene la informacion del pedido que se desea actualizar
        $pedido = Pedido::select()
                        ->where('pedidos.id', '=', $pedidoId)
                        ->get();
        
        // valida que el pedido exista
        if( !empty( $pedido['0']->id ) ) {
            $pedido['0']->estadopedido_id = $estadoPedId;
            $pedido['0']->save();

            return true;
        }

        return false;        
    }

    /**
     * Actualiza el pedido con la url del pdf-guia del transportador
     */
    public static function actualizarGuiaTransportador( $pedidoId, $url ) {
        // obtiene la informacion del pedido que se desea actualizar
        $pedido = Pedido::select()
                        ->where('pedidos.id', '=', $pedidoId)
                        ->get();
        
        // valida que el pedido exista
        if( !empty( $pedido['0']->id ) ) {
            $pedido['0']->url_guia = $url;
            $pedido['0']->save();

            return true;
        }

        return false; 
    }

    /**
     * Obtiene el pedido activo de un usuario
     */
    public static function obtenerPedidoPorUsuario( $usuarioId ) {
		  $data = Pedido::select()
                ->where('usuario_id', $usuarioId)
                ->where('carrito', 1)
                ->get();
    	return $data; 
    }

    /**
     * Obtiene la información del pedido activo para un usuario
     */
    public static function obtenerInfoPedido( $usuarioId ) {
      $data = Pedido::select()
                    ->join('pedidosdetalles', 'pedidosdetalles.pedido_id', '=', 'pedidos.id')
                    ->join('items', 'items.id', '=', 'pedidosdetalles.item_id')
                    ->where('pedidos.usuario_id', '=', $usuarioId)
                    ->where('pedidos.carrito', '=', 1)
                    ->get();
      return $data;
    }

    /**
     * Obtiene el pedido que el cliente ha aprobado para validarlo (unidades disponibles contra datax)
     */
    public static function obtenerPedidoValidar( $usuarioId ) {
      $data = Pedido::select()
                    ->join('pedidosdetalles', 'pedidosdetalles.pedido_id', '=', 'pedidos.id')
                    ->join('items', 'items.id', '=', 'pedidosdetalles.item_id')
                    ->where('pedidos.usuario_id', '=', $usuarioId)
                    ->where('pedidos.carrito', '=', 1)
                    ->get();
      return $data;      
    }

    /**
     * Actualiza el número de pedido web
     */
    public static function actualizarPedidoWeb( $usuarioId, $mpId ) {
      // obtiene la informacion del pedido que se desea actualizar
      $pedido = Pedido::select()
                      ->where('pedidos.usuario_id', '=', $usuarioId)
                      ->where('pedidos.carrito', '=', 1)
                      ->get();
      
      // valida que el pedido exista
      if( !empty( $pedido['0']->id ) ) {
          $pedido['0']->carrito = 0;
          $pedido['0']->mercadopago = $mpId;
          $pedido['0']->save();

          return $pedido['0']->id;
      }

      return false;        
    }  
    
    /**
     * Cambia el estado del pedido rechazado por pago
     */
    public static function rechazarPedido( $idPreference ) {
      // obtiene la información del pedido que se desea inactivar y rechazar por pago
      $pedido = Pedido::select()
                      ->where('pedidos.mercadopago', '=', $idPreference)                    
                      ->get();

      // valida que el pedido exista
      if( !empty( $pedido['0']->id ) ) {
        $pedido['0']->carrito = 0;
        $pedido['0']->estadopedido_id = 6;
        $pedido['0']->save();

        return $pedido['0']->id;
      }

      return false;
    }

    /**
     * Cambia el estado del pedido a pendiente por validacion de pago o rechaso del mismo
     */
    public static function pendientePedido( $idPreference ) {
      // obtiene la información del pedido que se desea reactivar
      $pedido = Pedido::select()
                      ->where('pedidos.mercadopago', '=', $idPreference)                    
                      ->get();

      // valida que el pedido exista
      if( !empty( $pedido['0']->id ) ) {
        $pedido['0']->carrito = 0;
        $pedido['0']->estadopedido_id = 4;
        $pedido['0']->save();

        return $pedido['0']->id;
      }

      return false;      
    }

    /**
     * Obtiene un pedido específico de un cliente
     */
    public static function obtenerPedidoWebCliente($userId, $pedidoId) {
      $data = Pedido::select()
                    ->join('usuarios', 'usuarios.id', '=', 'pedidos.usuario_id')
                    ->join('pedidosdetalles', 'pedidosdetalles.pedido_id', '=', 'pedidos.id')
                    ->where('pedidos.usuario_id', '=', $userId)
                    ->where('pedidos.id', '=', $pedidoId)
                    ->get();

      return $data;
    }  

    /**
     * Obtiene un pedido específico de un cliente que se encuentre activo
     */
    public static function obtenerPedidoActivoCliente($userId) {
      $data = Pedido::select()
                    ->join('usuarios', 'usuarios.id', '=', 'pedidos.usuario_id')
                    ->join('pedidosdetalles', 'pedidosdetalles.pedido_id', '=', 'pedidos.id')
                    ->where('pedidos.usuario_id', '=', $userId)
                    ->where('pedidos.carrito', '=', 1)
                    ->get();

      return $data;
    }  

    /**
     * Se obtinen los pedidos que se requiere sincronizar
     */
    public static function obtenerPedidosSincronizar() {
		  $data = Pedido::select()
                      ->join('usuarios as usu', 'usu.id', '=', 'pedidos.usuario_id')
                      ->join('usuarios as vend', 'vend.id', '=', 'pedidos.vendedor_id')
                      ->join('tipopagos', 'tipopagos.id', '=', 'pedidos.tipopago_id')
                      ->join('estadopedidos', 'estadopedidos.id', '=', 'pedidos.estadopedido_id')
                      ->join('pedidosdetalles', 'pedidosdetalles.pedido_id', '=', 'pedidos.id')
                      ->join('items', 'items.id', '=', 'pedidosdetalles.item_id')
                      ->where('pedidos.sincronizado', '=', 0)
                      ->where('pedidos.carrito', '=', 0)
                      ->get();  
      return $data;    
    }
}