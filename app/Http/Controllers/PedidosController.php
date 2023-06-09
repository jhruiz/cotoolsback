<?php

namespace App\Http\Controllers;

use App\Pedido;
use App\Usuario;
use App\Pedidosdetalle;
use App\Configuraciondato;
use App\Imagenesitem;
use App\Item;
use App\Impuesto;
use App\Listaprecio;
use App\Saldo;
use App\Mail\Pedidos;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class PedidosController extends Controller
{
    /*
    * Envia correo de creación de cliente
    */
    public function enviarCorreoPedido($userId, $pedidoId) {

        $items = Pedidosdetalle::obtenerDetallesPedido( $pedidoId );
        
        // se genera el número del pdido
        $numeroPedido = $this->obtenerNumeroPedido($items);

        $subTtal = 0;
        $dcto = 0;
        $subTtalNeto = 0;
        $iva = 0;
        $ttalPagar = 0;
        
        // se procesa la información de cada item
        foreach( $items as $key => $val ) {
            
            //verifica si tiene el iva incluido para calcularlo
            if ( $val->impto_incluido == 1 ) {
                $items[$key]->baseTtal = $val->vlr_item * $val->cantidad;
                $items[$key]->tasaImp = ( $val->vlr_impuesto/100 ) + 1;
                $items[$key]->vlrBase = number_format($val->vlr_item / $items[$key]->tasaImp, 2, '.', '');
                $items[$key]->vlrBaseTtal = number_format(($val->vlr_item / $items[$key]->tasaImp) * $val->cantidad, 2, '.', '');
                $items[$key]->vlrIva = number_format($val->vlr_item - $items[$key]->vlrBase, 2, '.', '');
            } else {
                $items[$key]->baseTtal = $val->vlr_item * $val->cantidad;
                $items[$key]->tasaImp = ( $val->vlr_impuesto/100 ) + 1;                            
                $items[$key]->vlrBase = number_format($val->vlr_item, 2, '.', '');
                $items[$key]->vlrBaseTtal = number_format(($val->vlr_item / $items[$key]->tasaImp) * $val->cantidad, 2, '.', '');
                $items[$key]->vlrIv = number_format($val->vlr_item * $val->vlr_item, 2, '.', '');
            }                    

            $subTtal += $items[$key]->vlrBase * $val->cantidad;
            $iva += $items[$key]->vlrIva * $val->cantidad;
        }
    
        $items->iva = $iva;
        $items->subTtalNeto = $subTtal - $dcto;  
        $items->ttalPagar = number_format($items->subTtalNeto + $iva, 2, '.', ''); 
        $items->numeroPedido = $numeroPedido; 

        //obtiene la información de los usuarios a los que debe enviarle el correo de creacion de tercero
        $nombre = 'infpedido';
        $correos = Configuraciondato::obtenerConfiguracion($nombre)['0']->valor;

        // //se obtienen los email configurados para enviar el correo (destinatarios)
        $arrMails = explode(",", $correos);

        // Mail::to($pedido['0']->email)->send(new Pedidos((object) $pedido));
        Mail::to('jaiber.ruiz@hotmail.com')->send(new Pedidos((object) $items));
        //se envian los correos configurados
        foreach($arrMails as $m) {
            sleep(2);
            Mail::to($m)->send(new Pedidos((object) $items));
        }
    }    
    
    /**
     * Retorna la información de todos los pedidos registrados en la base de datos
     */
    public function obtenerPedidos(){

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // Obtiene los pedidos registrados
            $result = Pedido::obtenerPedidos();

            // valida si se obtuvo el registro de los pedidos
            if( $result['0']->id ) {
                $resp['estado'] = true;
                $resp['data'] = $result;
            } else {
                $mensaje = 'No fue posible obtener los pedidos.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp; 

    }

    /**
     * Obtiene todos los pedidos relacionados a un cliente en particular
     */
    public function obtenerPedidosCliente(Request $request) {
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        $userId = $request['userId'];

        try {

            // se obtienen los pedidos de un cliente en particular
            $pedidos = Pedido::obtenerPedidosCliente($userId); 
            
            // valida si se econtraron registros
            if( !empty( $pedidos ) ) {
                $resp['estado'] = true;
                $resp['data'] = $pedidos;
            } else {
                $resp['mensaje'] = 'El cliente no tiene pedidos registrados.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }

    /**
     * Obtiene el precio del producto basado en las listas de precios
     * que pertenecen al cliente en datax
     */
    public function obtenerPrecioLista($listaPrecio, $itemId) {

        $precio = null;
        try {
            // obtiene la lista de precios de un producto
            $listaPrc = Listaprecio::obtenerListaprecios( $itemId );
            
            // obtiene el precio del producto
            $numLista = 'precio' . $listaPrecio;
            $precio = $listaPrc['0']->$numLista;
            
        } catch(Throwable $e) {
            return null;
        }

        return $precio;
        
    }

    /**
     * Guarda un pedido
     */
    public function guardarPedido( Request $request ) {

        $codItem = $request['item'];
        $cantItem = $request['cant'];
        $codBenf = $request['codBenf'];
        $usuarioId = $request['usuarioId'];
        $descItem = $request['desc'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // valida que se hayan enviado los datos del codigo de usuario, el usuario y el número del pedido
            if(!empty($codItem) && !empty($cantItem) && !empty($usuarioId)) {              

                // se verifica si hay un pedido abierto para el cliente
                $pedido = Pedido::obtenerPedidoPorUsuario( $usuarioId );
                $regId = '';

                // se obtiene la información del usuario
                $userInfo = Usuario::obtenerUsuarioPorId( $usuarioId );
                
                // valida si ya existe un pedido para el usuario, sino, lo crea
                if ( !empty( $pedido['0']->id ) ) {
                    $regId = $pedido['0']->id;
                } else {

                    $data = array(
                        'fechapedido' => date('Y-m-d H:i:s'),
                        'usuario_id' => $usuarioId,
                        'vendedor_id' => $userInfo['0']->usuario_id,
                        'detalle' => 'Venta Miggo web',
                        'tipopago_id' => '2',
                        'diascredito' => '30',
                        'listaprecio_id' => $userInfo['0']->listaprecio,
                        'sincronizado' => '0',
                        'estadopedido_id' => '4',
                        'created_at' => date('Y-m-d H:i:s'),
                        'carrito' => '1'
                    );
    
                    // Guarda la informacion del pedido
                    $regId = Pedido::guardarPedido( $data );
                }

                // si se obtiene el id del pedido, guarda el detalle del mismo
                if( !empty( $regId ) ) {

                    // se obtiene información del item por código
                    $itemInfo = Item::obtenerItemPorCodigo( $codItem );

                    // valida si el producto ya ha sido cargado previamente para ese usuario                 
                    $respDet = Pedidosdetalle::validarActualizarPedido($regId, $itemInfo['0']->id, $cantItem);

                    // valida si se actualizó el registro del producto para el pedido
                    if( empty($respDet['0']->id ) ){

                        // obtiene el precio del item basado en el precio de la lista del usuario
                        $precioLista = $this->obtenerPrecioLista($userInfo['0']->listaprecio, $itemInfo['0']->id);

                        // se obtiene el impuesto asociado al producto
                        $iptoItem = Impuesto::obtenerImpuestoId( $itemInfo['0']->impuesto_id );                        

                        $dataDet = array(
                            'pedido_id' => $regId,
                            'fecha_agregado' => date('Y-m-d H:i:s'),
                            'item_id' => $itemInfo['0']->id,
                            'cantidad' => $cantItem,
                            'vlr_item' => $precioLista,
                            'vlr_impuesto' => $iptoItem['0']->tasa,
                            'created_at' => date('Y-m-d H:i:s')                            
                        );

                        // guarda el detalle del pedido
                        $idDet = Pedidosdetalle::guardarDetallePedido( $dataDet );
                        
                        if( !empty($idDet) ){
                            $resp['estado'] = true;
                        } else {
                            $mensaje = 'No fue posible realizar el registro. Por favor, inténtelo nuevamente.';
                        }
                    } else {
                        $resp['estado'] = false;
                        $resp['mensaje'] = 'El item ya se encuentra en el carrito de compras con ' . $respDet['0']->cantidad . ' unidades.';
                    }
                } else {
                    $resp['estado'] = false;
                    $resp['mensaje'] = 'No fue posible realizar el registro del pedido. Por favor, inténtelo nuevamente.';                    
                }
     
            } else {
                $resp['mensaje'] = 'Debe ingresar un usuario y un producto.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;
    }

    /**
     * Actualiza el estado del pedido a pagado
     */
    public function actualizarEstadoPago( $id ) {
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // Valida que se enviara el id del pedido a actualizar
            if( !empty( $id ) ) {

                $rp = Pedido::actualizarEstadoPago( $id );

                // valida si fue posible realizar la actualización del estado pago
                if( $rp ) {
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
     * Se obtienen los detalles del pedido
     */
    public function obtenerDetallePedido( Request $request ) {
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            $userId = $request['userId'];
            $codBenf = $request['codBenf'];

            // Valida que se enviara el id del pedido a actualizar
            if( !empty($userId) ) {

                // se obtiene el pedido del cliente
                $items = Pedido::obtenerInfoPedido( $userId );

                if( !empty( $items['0']->id ) ) {
                    // valores para la factura
                    $subTtal = 0;
                    $dcto = 0;
                    $subTtalNeto = 0;
                    $iva = 0;
                    $ttalPagar = 0;
                    
                    // se procesa la información de cada item
                    foreach( $items as $key => $val ) {
                        
                        //verifica si tiene el iva incluido para calcularlo
                        if ( $val->impto_incluido == 1 ) {
                            $items[$key]->baseTtal = $val->vlr_item * $val->cantidad;
                            $items[$key]->tasaImp = ( $val->vlr_impuesto/100 ) + 1;
                            $items[$key]->vlrBase = number_format($val->vlr_item / $items[$key]->tasaImp, 2, '.', '');
                            $items[$key]->vlrBaseTtal = number_format(($val->vlr_item / $items[$key]->tasaImp) * $val->cantidad, 2, '.', '');
                            $items[$key]->vlrIva = number_format($val->vlr_item - $items[$key]->vlrBase, 2, '.', '');
                        } else {
                            $items[$key]->baseTtal = $val->vlr_item * $val->cantidad;
                            $items[$key]->tasaImp = ( $val->vlr_impuesto/100 ) + 1;                            
                            $items[$key]->vlrBase = number_format($val->vlr_item, 2, '.', '');
                            $items[$key]->vlrBaseTtal = number_format(($val->vlr_item / $items[$key]->tasaImp) * $val->cantidad, 2, '.', '');
                            $items[$key]->vlrIv = number_format($val->vlr_item * $val->vlr_item, 2, '.', '');
                        }
    
                        $subTtal += $items[$key]->vlrBase * $val->cantidad;
                        $iva += $items[$key]->vlrIva * $val->cantidad;
    
                        //se obtienen las imagenes de cada item
                        $img = Imagenesitem::obtenerImagenItem($val->item_id);
                        if( !empty( $img['0']->id ) ) {
                            $items[$key]->imagen = $img['0']->url;
                        } else {
                            $items[$key]->imagen = '';
                        }
                    }
    
                    $subTtalNeto = $subTtal - $dcto;  
                    $ttalPagar = number_format($subTtalNeto + $iva, 2, '.', '');                
    
                    // valida si fue posible realizar la actualización del estado pago
                    if( $items['0']->id ) {
                        $resp['data'] = $items;
                        $resp['ttles'] = array($subTtal, $dcto, $subTtalNeto, $iva, $ttalPagar);
                        $resp['estado'] = true;
                    } else {
                        $resp['mensaje'] = 'El pedido seleccionado no fue encontrado en los registros';
                    }
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
     * Contrasta la información obtendida de datax (unidades) vs las unidades 
     * pedidas por el cliente
     */
    public function contrastarInfoCantidades($detPedido, $cantidad) {
        $resp = array('estado' => true, 'data' => null);

        $compResult = [];
        foreach( $detPedido as $key => $val ) {

            // se actualiza la cantidad solicitada y la cantidad disponible
            Pedidosdetalle::obtenerProductoEnDetalle($detPedido['0']->pedido_id, $val->item_id, $val->cantidad, $cantidad[$val->item_id]);
            
            // verifica si la cantidad solicitada por el cliente es mayor a la existente
            if( $val->cantidad > $cantidad[$val->item_id] ) {
                $compResult[] = array(
                    'item_id' => $val->item_id,
                    'descripcion' => $val->descripcion,
                    'cantidad' => $val->cantidad,
                    'disponible' => $cantidad[$val->item_id]
                );

                $resp['estado'] = false;
            }
        }

        $resp['data'] = $compResult;

        return $resp;
    }

    /**
     * Obtiene los saldos disponibles de un arreglo de productos
     */
    public function obtenerCantidadesDisponibles( $idsItems ) {
        $cantItems = [];
        try {
            // se recorren los ids de los items para obtener las cantidades de cada producto
            foreach( $idsItems as $val ) {
                // se obtiene la cantidad de un item
                $cntItem = Saldo::obtenerSaldos( $val );
                $cantItems[$val] = $cntItem['0']->saldoactual;
            }
            
        } catch(Throwable $e) {
            return null;
        }

        return $cantItems;
    }

    /**
     * Valida que los productos cuenten con las unidades disponibles suficientes
     * en el stock para realizar el pedido
     */
    public function validarPedido( Request $request ) {
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            $userId = $request['userId'];            

            // Valida que se enviara el id del cliente
            if( !empty( $userId ) ) {

                // obtiene el pedido que se desea validar
                $detPedido = Pedido::obtenerPedidoValidar( $userId );

                // valida si existe pedido para validar
                if( !empty( $detPedido['0']->id ) ) {

                    // obtiene los codigos de los items del pedido
                    $idsItems = [];                    
                    foreach( $detPedido as $key => $val ) {
                        $idsItems[] = $val->item_id;
                    }

                    // obtiene las cantidades disponibles de los items
                    $cantidades = $this->obtenerCantidadesDisponibles($idsItems);
                    
                    // valida si existen unidades suficientes en el stock para realizar el pedido
                    $compareRes = $this->contrastarInfoCantidades($detPedido, $cantidades);

                    if( !$compareRes['estado'] ) {
                        $resp['data'] = $compareRes['data'];
                        $resp['mensaje'] = 'Productos sin unidades suficientes en el stock';
                    } else {
                        $resp['estado'] = true;

                    }

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
     * Actualiza las unidades pedidas por el cliente con las unidades disponibles.
     * Esto se realiza solo para los productos que tienen unidades pedidas por encima de 
     * las unidades disponibles
     */
    public function actualizarUnidadesPedido( Request $request ) {
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            $userId = $request['userId'];

            // Valida que se enviara el id del cliente
            if( !empty( $userId ) ) {

                // obtiene el pedido que se desea actualizar
                $detPedido = Pedido::obtenerPedidoValidar( $userId );

                // valida si existe pedido para actualizar
                if( !empty( $detPedido['0']->id ) ) {

                    // obtiene los ids de los items del pedido
                    $idsItems = [];                    
                    foreach( $detPedido as $key => $val ) {
                        $idsItems[] = $val->id;
                    }

                    // obtiene las cantidades disponibles de los items desde datax
                    $cantidades = $this->obtenerCantidadesDisponibles($idsItems);
                    
                    // actualiza la cantidad pedida por el cliente con la cantidad disponible
                    $req = true;                    
                    foreach( $detPedido as $key => $val ) {

                        // elimina el registro ya que no existen unidades disponibles del producto
                        if( $cantidades[$val->item_id] < 1) {
                            Pedidosdetalle::eliminarItemPedido( $val->item_id, $val->pedido_id );
                        } 

                        // actualizo cuando la cantidad disponible sea menor a la cantidad pedida
                        if( $cantidades[$val->item_id] < $val->cantidad ) {
                            $req = Pedidosdetalle::actualizarCantidadItem( $val->item_id, $val->pedido_id, $cantidades[$val->item_id] );
                            if( !$req ) {
                                break;
                            }
                        }
                    }

                    $resp['estado'] = $req;

                    if( $req ) {
                        $resp['mensaje'] = 'Unidades actualizadas de forma correcta.';                        
                    } else {
                        $resp['mensaje'] = 'Se presentó un error. Por favor, inténtelo nuevamente.';
                    }
                } else {
                    $resp['mensaje'] = 'No fue posible obtener el pedido. Por favor, inténtelo nuevamente.';
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
     * Retorna el pedido activo de un cliente específico
     */
    public function obtenerPedidoSimple( $userId ){

        // valida si se envio el cliente como parametro
        if( !empty( $userId ) ) {
            // se obtiene el pedido del cliente
            return Pedido::obtenerInfoPedido( $userId );
        } else {
            return null;
        }
    }

    /**
     * Aprueba el pedido agregando la información del pedido web registrado en datax
     */
    public function aprobarPedido( Request $request ){

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        $userId = $request['userId'];
        $mpId = $request['mpId'];

        if( !empty($userId) ) {

            // actualiza el numero de pedido web obtenido de datax
            $pedidoId = Pedido::actualizarPedidoWeb( $userId, $mpId );
            if( $pedidoId ) {
                // obtiene información básica del pedido
                $infoPed = Pedidosdetalle::obtenerPedidoWeb($userId, $pedidoId);

                // se envia información del pedido
                $this->enviarCorreoPedido($userId, $pedidoId);

                // se obtiene el total a pagar en la factura
                $ttalPagar = 0;
                foreach($infoPed as $key => $val) {
                    $ttalPagar += $val->total;
                }

                $resp['estado'] = true;
                $resp['data'] = $infoPed;
                $resp['total'] = $ttalPagar;
            } 
        }

        return $resp;
    }

    /**
     * Actualiza el estado del pedido
     */
    public function actualizarEstadoPedido( Request $request ) {
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        $pedidoId = $request['pedidoId'];
        $estadoPedId = $request['idEst'];

        try {

            // Valida que se enviara el id del pedido a actualizar
            if( !empty( $pedidoId ) && !empty($estadoPedId) ) {

                $rp = Pedido::actualizarEstadoPedido( $pedidoId, $estadoPedId );

                // valida si fue posible realizar la actualización del estado pago
                if( $rp ) {
                    $resp['estado'] = true;
                } else {
                    $resp['mensaje'] = 'No fue posible realizar la actualización del estado.';
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
     * Actualiza el pedido con la url de la guia de la transportadora
     */
    public function actualizarUrlGuia(Request $request) {
        $pedidoId = $request['pedidoId'];
        $documento = $request['documento'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // valida que se hayan enviado los datos del documento y del usuario
            if(!empty($pedidoId) && !empty($documento) ) {
    
                // Guarda la informacion del documento y el usuario
                $respAct = Pedido::actualizarGuiaTransportador( $pedidoId, $documento );

                // valida si el registro fue almacenado de forma correcta
                if( $respAct ) {
                    $resp['estado'] = true;
                } else {
                    $mensaje = 'Ups! Algo salio mal en la carga del archivo.';
                }
     
            } else {
                $resp['mensaje'] = 'Debe ingresar una guia para el pedido.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }

    /**
     * Obtiene la compra activa de un cliente
     */
    public function obtenerCantidadItems( Request $request ) {
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );
        $clienteId = $request['userId'];

        try {

            //obtiene el pedido activo para un cliente y su detalle
            $infoPed = Pedido::obtenerPedidoActivoCliente( $clienteId );
            
            if( !empty( $infoPed['0']->id ) ) {
                $resp['estado'] = true;
                $resp['data'] = $infoPed;
            } else {
                $resp['mensaje'] = 'No fue posible obtener los registros';
            }

        }catch(Throwable $e) {
            $resp = array( 'estado' => false, 'data' => null, 'mensaje' => 'Se presentó un error' );
        }

        return $resp;
    }

    /**
     * Se obtienen los pedidos que se desean sincronizar a la aplicación contable
     */
    public function sincronizarpedido() {
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            //obtiene los pedidos que se deben sincronizar
            $infoPed = Pedido::obtenerPedidosSincronizar();
            
            if( !empty( $infoPed['0']->id ) ) {
                $resp['estado'] = true;
                $resp['data'] = $infoPed;
            } else {
                $resp['mensaje'] = 'No fue posible obtener los registros';
            }

        }catch(Throwable $e) {
            $resp = array( 'estado' => false, 'data' => null, 'mensaje' => 'Se presentó un error' );
        }

        return $resp;
    }

    /**
     * Se genera el número del pedido
     */
    public function obtenerNumeroPedido($items) {
        $numPedido = '';

        $arrFecha = explode(' ', $items['0']->fechapedido);

        $numPedido = $items['0']->id . $items['0']->usuario_id . str_replace(':', '', $arrFecha['1']);  

        return $numPedido;
    }

    /**
     * Rechazar pedido por fallos en el pago
     */
    public function rechazarPedido( Request $request ) {
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            $idPreference = $request['idPreference'];

            // Valida que se enviara el id del pedido a actualizar
            if( !empty( $idPreference ) ) {

                $rp = Pedido::rechazarPedido( $idPreference );

                // valida si fue posible realizar la actualización del estado pago
                if( $rp ) {
                    $resp['estado'] = true;
                } else {
                    $resp['mensaje'] = 'No fue posible encontrar el pedido.';
                }
                
            } else {
                $resp['mensaje'] = 'No fue posible encontrar el pedido.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;
    }

    /**
     * Pasa a estado pendiente de pago un pedido
     */
    public function pendientePedido( Request $request ) {
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            $idPreference = $request['idPreference'];

            // Valida que se enviara el id del pedido a actualizar
            if( !empty( $idPreference ) ) {

                $rp = Pedido::pendientePedido( $idPreference );

                // valida si fue posible realizar la actualización del estado pago
                if( $rp ) {
                    $resp['estado'] = true;
                } else {
                    $resp['mensaje'] = 'No fue posible encontrar el pedido.';
                }
                
            } else {
                $resp['mensaje'] = 'No fue posible encontrar el pedido.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;
    }

    /**
     * Se aprueba el pago y el pedido
     */
    public function aprobadoPedido( Request $request ) {
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            $idPreference = $request['idPreference'];

            // Valida que se enviara el id del pedido a actualizar
            if( !empty( $idPreference ) ) {

                $rp = Pedido::aprobadoPedido( $idPreference );

                // valida si fue posible realizar la actualización del estado pago
                if( $rp ) {
                    $resp['estado'] = true;
                } else {
                    $resp['mensaje'] = 'No fue posible encontrar el pedido.';
                }
                
            } else {
                $resp['mensaje'] = 'No fue posible encontrar el pedido.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;
    }
    
    /**
     * Llamado por webhook desde mercadopago
     */
    public function whmercadopago( Request $request ) {

        $paymentId = $request->get('payment_id');

        $client = new Client();

        $url = 'https://api.mercadopago.com/v1/payments/' . $paymentId;
        $token = 'APP_USR-4653158926500716-050220-a3db41d66b87cee0a4bf985ecd850f14-1363105107';

        $response = $client->request(
            'GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer '. $token
                    ]
                ]
        );
        
        if($response->getStatusCode() == '200') {
            $content = json_decode((string) $response->getBody()->getContents());

            if(!empty($content)) {
                $idPedido = array_slice(explode('/', $content->notification_url), -1)[0]; 
                $statusPay = $content->status;

                if ( $content->status == 'approved' ) {

                    $rp = Pedido::actualizarEstadoMercadoPago( $idPedido, '5' );

                } else if( $content->status == 'pending' ) {

                    $rp = Pedido::actualizarEstadoMercadoPago( $idPedido, '4' );

                } else if ( $content->status == 'rejected' ) {
                    
                    $rp = Pedido::actualizarEstadoMercadoPago( $idPedido, '6' );
                
                }
            }
        }
    }

    public function whnotifications( $id, Request $request ) {
        echo "entra por aqui " . $id;
    }

}