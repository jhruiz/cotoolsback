<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    /**
     * Se obtiene la información de todos los pedidos registrados en la aplicacion
     */
    public static function obtenerPedidos( ) {
		  $data = Pedido::select( 'pedidos.id', 'pedidos.nro_pdweb', 'pedidos.estadopago', 
                              'pedidos.created', 'usuarios.nombre', 'usuarios.identificacion', 
                              'usuarios.email', 'estadospedidos.descripcion')
                ->join('usuarios', 'usuarios.id', '=', 'pedidos.usuario_id')
                ->join('estadospedidos', 'estadospedidos.id', '=', 'pedidos.estadospedido_id')
                ->groupBy('pedidos.id')  
                ->get();
    	return $data;     	
    }

    /**
     * Obtiene los pedidos realizados por un cliente especifico
     */
    public static function obtenerPedidosCliente($userId) {
      $data = Pedido::select('pedidos.id', 'pedidos.nro_pdweb', 'pedidos.updated_at', 'estadospedidos.descripcion')
                    ->join('estadospedidos', 'estadospedidos.id', '=', 'pedidos.estadospedido_id')
                    ->where('pedidos.usuario_id', '=', $userId)
                    ->where('pedidos.nro_pdweb', '<>', null)
                    ->get();

      return $data;
    }

    /**
     * Guarda el pedido y retorna el id
     */
    public static function guardarPedido( $data ) {
	    $id = Pedido::insertGetId([
            'cod_benf' => $data['cod_benf'],
            'estadopago' => 0,
            'estadospedido_id' => '1',
            'usuario_id' => $data['usuario_id'],                    
            'created' => $data['created']
          ]);	
          
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
            $pedido['0']->estadospedido_id = $estadoPedId;
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
                ->where('nro_pdweb', null)
                ->get();
    	return $data; 
    }

    /**
     * Obtiene la información del pedido activo para un usuario
     */
    public static function obtenerInfoPedido( $usuarioId ) {
      $data = Pedido::select()
                    ->join('pedidosdetalles', 'pedidosdetalles.pedido_id', '=', 'pedidos.id')
                    ->where('pedidos.usuario_id', '=', $usuarioId)
                    ->where('pedidos.nro_pdweb', '=', null)
                    ->where('pedidosdetalles.estado', '=', '1')
                    ->get();
      return $data;
    }

    /**
     * Obtiene el pedido que el cliente ha aprobado para validarlo (unidades disponibles contra datax)
     */
    public static function obtenerPedidoValidar( $usuarioId ) {
      $data = Pedido::select()
                    ->join('pedidosdetalles', 'pedidosdetalles.pedido_id', '=', 'pedidos.id')
                    ->where('pedidos.usuario_id', '=', $usuarioId)
                    ->where('pedidos.nro_pdweb', '=', null)
                    ->get();
      return $data;      
    }

    /**
     * Actualiza el número de pedido web
     */
    public static function actualizarPedidoWeb( $usuarioId, $pdWeb ) {
      // obtiene la informacion del pedido que se desea actualizar
      $pedido = Pedido::select()
                      ->where('pedidos.usuario_id', '=', $usuarioId)
                      ->where('pedidos.nro_pdweb', '=', null)
                      ->get();
      
      // valida que el pedido exista
      if( !empty( $pedido['0']->id ) ) {
          $pedido['0']->nro_pdweb = $pdWeb;
          $pedido['0']->save();

          return true;
      }

      return false;        
  }    

    /**
     * Obtiene un pedido específico de un cliente
     */
    public static function obtenerPedidoWebCliente($userId, $pdWeb) {
      $data = Pedido::select()
                    ->join('usuarios', 'usuarios.id', '=', 'pedidos.usuario_id')
                    ->join('pedidosdetalles', 'pedidosdetalles.pedido_id', '=', 'pedidos.id')
                    ->where('pedidos.usuario_id', '=', $userId)
                    ->where('pedidos.nro_pdweb', '=', $pdWeb)
                    ->get();

      return $data;
    }  
}