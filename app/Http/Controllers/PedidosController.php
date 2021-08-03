<?php

namespace App\Http\Controllers;

use App\Pedido;
use App\Usuario;
use App\Pedidosdetalle;
use App\Configuraciondato;
use App\Imagenesitem;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class PedidosController extends Controller
{
    /*
    * Envia correo de creación de cliente
    */
    public function enviarCorreoPedido($userId, $pdWeb) {

        // $pedidos = Pedido::obtenerPedidosCliente($userId); 
       
        // //obtiene la información de los usuarios a los que debe enviarle el correo de creacion de tercero
        // $nombre = 'infpedido';
        // $correos = Configuraciondato::obtenerConfiguracion($nombre)['0']->valor;

        // //se obtienen los email configurados para enviar el correo (destinatarios)
        // $arrMails = explode(",", $correos);

        // //se envian los correos configurados
        // foreach($arrMails as $m) {
        //     Mail::to($m)->send(new usuarioCreado((object) $data));
        // }
    }    
    
    /**
     * Retorna la información de todos los pedidos registrados en la base de datos
     */
    public function obtenerPedidos(){

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen los pedidos
            $pedidos = Pedido::obtenerPedidos(); 
            
            // valida si se econtraron registros
            if( !empty( $pedidos ) ) {
                $resp['estado'] = true;
                $resp['data'] = $pedidos;
            } else {
                $resp['mensaje'] = 'No se encontraron los pedidos';
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
    public function obtenerPrecioLista($codBenf, $codItem) {

        $resp = null;
        try {
            $urlDatax = Configuraciondato::obtenerConfiguracion('urldatax');
    
            // obtiene el precio configurado para el cliente desde datax
            $client = new Client();
            $response = $client->request('GET', $urlDatax['0']->valor . 'get-item-price/' . $codBenf . '/' . $codItem);
            
            // verifica que la respuesta sea correcta
            if($response->getStatusCode() == '200') {
                $content = (string) $response->getBody()->getContents();
                $resp = json_decode($content);

                if( !empty( $resp ) ) {
                    return (array)$resp;
                } else {
                    return null;
                }
                
            } else {
                return null;
            }             
        } catch(Throwable $e) {
            return null;
        }

        return $resp;
        
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
            if(!empty($codItem) && !empty($cantItem) && !empty($codBenf) && !empty($usuarioId)) {              

                // se verifica si hay un pedido abierto para el cliente
                $pedido = Pedido::obtenerPedidoPorUsuario( $usuarioId );
                $regId = '';

                // valida si ya existe un pedido para el usuario, sino, lo crea
                if ( !empty( $pedido['0']->id ) ) {
                    $regId = $pedido['0']->id;
                } else {
                    $data = array(
                        'cod_benf' => $codBenf,
                        'estadopago' => '0',
                        'usuario_id' => $usuarioId,
                        'created' => date('Y-m-d H:i:s')
                    );
    
                    // Guarda la informacion del pedido
                    $regId = Pedido::guardarPedido( $data );
                }

                // si se obtiene el id del pedido, guarda el detalle del mismo
                if( !empty( $regId ) ) {

                    // valida si el producto ya ha sido cargado previamente para ese usuario                 
                    $respDet = Pedidosdetalle::validarActualizarPedido($regId, $codItem, $cantItem);

                    // valida si se actualizó el registro del producto para el pedido
                    if( empty($respDet['0']->id ) ){

                        // obtiene el precio del item basado en el precio de la lista del usuario
                        $precioLista = $this->obtenerPrecioLista($codBenf, $codItem);

                        $dataDet = array(
                            'pedido_id' => $regId,
                            'codigo_item' => $codItem,
                            'desc' => $descItem,
                            'cantidad'  => $cantItem,
                            'precioventaunit' => $precioLista['precio'],  
                            'estado' => '1',                          
                            'vlriva' => $precioLista['ivaInc'],
                            'tasaiva' => $precioLista['tasaIva'],
                            'created' => date('Y-m-d H:i:s')
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
            if( !empty( $codBenf ) && !empty($userId) ) {

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
    public function contrastarInfoCantidades($detPedido, $respDtx) {
        $resp = array('estado' => true, 'data' => null);

        $compResult = [];
        foreach( $detPedido as $key => $val ) {

            // se actualiza la cantidad solicitada y la cantidad disponible
            Pedidosdetalle::obtenerProductoEnDetalle($detPedido['0']->pedido_id, $val->cod_item, $val->cantidad, $respDtx[$val->cod_item]);

            // verifica si la cantidad solicitada por el cliente es menor a la existente en datax
            if( ($val->cantidad > $respDtx[$val->cod_item] || $val->cantidad == 0) && $val->estado == '1' ) {
                $compResult[] = array(
                    'cod_item' => $val->cod_item,
                    'descripcion' => $val->descripcion,
                    'cantidad' => $val->cantidad,
                    'disponible' => $respDtx[$val->cod_item]
                );

                $resp['estado'] = false;
            }
        }

        $resp['data'] = $compResult;

        return $resp;
    }

    /**
     * Obtiene las unidades disponibles de productos específicos desde datax
     */
    public function obtenerUnidadesDisponiblesDatax( $codsItems ) {
        $urlDatax = Configuraciondato::obtenerConfiguracion('urldatax');
    
        // obtiene el precio configurado para el cliente desde datax
        $client = new Client();
        $response = $client->request('GET', $urlDatax['0']->valor . 'get-available-units/' . json_encode( $codsItems ));

        // verifica que la respuesta sea correcta
        if($response->getStatusCode() == '200') {
            $content = (string) $response->getBody()->getContents();
            return json_decode($content);
        } else {
            return null;
        }

        return null;

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
                    $codsItems = [];                    
                    foreach( $detPedido as $key => $val ) {
                        $codsItems[] = $val->cod_item;
                    }

                    // obtiene las cantidades disponibles de los items desde datax
                    $respDtx = (array)$this->obtenerUnidadesDisponiblesDatax( $codsItems );
                    
                    // valida si existen unidades suficientes en el stock para realizar el pedido
                    $compareRes = $this->contrastarInfoCantidades($detPedido, $respDtx);

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

                    // obtiene los codigos de los items del pedido
                    $codsItems = [];                    
                    foreach( $detPedido as $key => $val ) {
                        $codsItems[] = $val->cod_item;
                    }

                    // obtiene las cantidades disponibles de los items desde datax
                    $respDtx = (array)$this->obtenerUnidadesDisponiblesDatax( $codsItems );
                    
                    // actualiza la cantidad pedida por el cliente con la cantidad disponible
                    $req = true;                    
                    foreach( $detPedido as $key => $val ) {

                        // elimina el registro ya que no existen unidades disponibles del producto
                        if( $respDtx[$val->cod_item] < 1) {
                            Pedidosdetalle::eliminarItemPedido( $val->cod_item, $val->pedido_id );
                        } 

                        // actualizo cuando la cantidad disponible sea menor a la cantidad pedida
                        if( $respDtx[$val->cod_item] < $val->cantidad ) {
                            $req = Pedidosdetalle::actualizarCantidadItem( $val->cod_item, $val->pedido_id, $respDtx[$val->cod_item] );
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
        $pdWeb = $request['pdWeb'];

        if( !empty($userId) && !empty($pdWeb) ) {

            // actualiza el numero de pedido web obtenido de datax
            if(Pedido::actualizarPedidoWeb( $userId, $pdWeb )) {
                // obtiene información básica del pedido
                $infoPed = Pedidosdetalle::obtenerPedidoWeb($userId, $pdWeb);

                // se envia información del pedido
                $this->enviarCorreoPedido($userId, $pdWeb);

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

}