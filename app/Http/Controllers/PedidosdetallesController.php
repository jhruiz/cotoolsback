<?php

namespace App\Http\Controllers;

use App\Pedidosdetalle;
use App\Imagenesitem;
use Illuminate\Http\Request;

class PedidosdetallesController extends Controller
{
    
    /**
     * Retorna la información de todos los detalles de un pedido registrados en la base de datos
     */
    public function obtenerPedidoDetalles( Request $request )
    {

        $pedidoId = $request['pedidoId'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $pedidoId ) ) {

                // se obtienen los pedidos
                $items = Pedidosdetalle::obtenerDetallesPedido( $pedidoId ); 
                
                if( !empty( $items['0'] ) ) {

                    // valores para la factura
                    $subTtal = 0;
                    $dcto = 0;
                    $subTtalNeto = 0;
                    $iva = 0;
                    $ttalPagar = 0;
                    
                    // se procesa la información de cada item
                    foreach( $items as $key => $val ) {
                        
                        //verifica si tiene el iva incluido para calcularlo
                        if ( $val->vlriva == 1 ) {
                            $items[$key]->baseTtal = $val->precioventaunit * $val->cantidad;
                            $items[$key]->tasaImp = ( $val->tasaiva/100 ) + 1;
                            $items[$key]->vlrBase = number_format($val->precioventaunit / $items[$key]->tasaImp, 2, '.', '');
                            $items[$key]->vlrBaseTtal = number_format(($val->precioventaunit / $items[$key]->tasaImp) * $val->cantidad, 2, '.', '');
                            $items[$key]->vlrIva = number_format($val->precioventaunit - $items[$key]->vlrBase, 2, '.', '');
                        } else {
                            $items[$key]->baseTtal = $val->precioventaunit * $val->cantidad;
                            $items[$key]->tasaImp = ( $val->tasaiva/100 );
                            $items[$key]->vlrIva = number_format($val->precioventaunit * $items[$key]->tasaImp, 2, '.', '');
                            $items[$key]->vlrBase = number_format($val->precioventaunit, 2, '.', '');
                            $items[$key]->vlrBaseTtal = number_format(($val->precioventaunit / $items[$key]->tasaImp) * $val->cantidad, 2, '.', '');
                        }
    
                        $subTtal += $items[$key]->vlrBase * $val->cantidad;
                        $iva += $items[$key]->vlrIva * $val->cantidad;
    
                        //se obtienen las imagenes de cada item
                        $img = Imagenesitem:: obtenerImagenItem($val->cod_item);
                        if( !empty( $img['0']->id ) ) {
                            $items[$key]->imagen = $img['0']->url;
                        } else {
                            $items[$key]->imagen = '';
                        }
                    }
    
                    $subTtalNeto = $subTtal - $dcto;  
                    $ttalPagar = number_format($subTtalNeto + $iva, 2, '.', '');                
    
                    $resp['data'] = $items;
                    $resp['ttles'] = array($subTtal, $dcto, $subTtalNeto, $iva, $ttalPagar);
                    $resp['estado'] = true;

                } else {
                    $resp['mensaje'] = 'El pedido seleccionado no fue encontrado en los registros';
                }

            } else {
                $resp['mensaje'] = 'Debe seleccionar un pedido';
            }


        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;

    }

    /**
     * Guarda el detalle de un pedido
     */
    public function guardarDetallePedido( $pedidoId, $codItem, $cant, $precioVentaU, $vlrIva ) {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // valida que se hayan enviado los datos completos para el registro del detalle del pedido
            if( !empty( $pedidoId ) && !empty( $codItem ) && !empty( $cant ) && !empty( $precioVentaU ) && !empty( $vlrIva ) ) {

                $data = array(
                    'pedido_id' => $pedidoId,
                    'codigo_item' => $codItem,
                    'cantidad' => $cant,
                    'precioventaunit' => $precioVentaU,
                    'vlriva' => $vlrIva,
                    'created' => date('Y-m-d H:i:s')
                );

                // Guarda la informacion del detalle del pedido
                $regId = Pedidosdetalle::guardarDetallePedido( $data );

                // valida si el registro fue almacenado de forma correcta
                if( !empty( $regId ) ) {
                    $resp['estado'] = true;
                    $resp['data'] = $regId;
                } else {
                    $mensaje = 'No fue posible realizar el registro';
                }
     
            } else {
                $resp['mensaje'] = 'Debe ingresar un usuario y número de pedido.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;
    }

    /**
     * Elimina un item del pedido
     */
    public function eliminarItemPedido(Request $request) {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        $idItem = $request['idItem'];
        $idPed = $request['idPed'];

        try {

            // valida que se hayan enviado los datos completos para eliminar el item del pedido
            if( !empty( $idItem ) && !empty( $idPed ) ) {

                // Elimina la informacion del detalle del pedido
                $result = Pedidosdetalle::eliminarItemPedido( $idItem, $idPed );

                // valida si el registro fue eliminado de forma correcta
                if( $result ) {
                    $resp['estado'] = true;
                } else {
                    $mensaje = 'No fue posible eliminar el registro';
                }
     
            } else {
                $resp['mensaje'] = 'Debe ingresar un usuario y número de pedido.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }

    /**
     * Cambia la cantidad de un item solicitada por un cliente
     */
    public function cambiarCantidadItem( Request $request ) {
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        $codItem = $request['codItem'];
        $idPed = $request['idPed']; 
        $cant = $request['cant'];

        try {

            // valida que se hayan enviado los datos completos para actualizar el item del pedido
            if( !empty( $codItem ) && !empty( $idPed ) && !empty( $cant ) ) {

                // Guarda la informacion del detalle del pedido
                $result = Pedidosdetalle::actualizarCantidadItem( $codItem, $idPed, $cant );

                // valida si el registro fue almacenado de forma correcta
                if( $result ) {
                    $resp['estado'] = true;
                } else {
                    $mensaje = 'No fue posible realizar el registro';
                }
     
            } else {
                $resp['mensaje'] = 'Debe ingresar un usuario y número de pedido.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;  
    }
}