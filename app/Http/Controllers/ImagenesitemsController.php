<?php

namespace App\Http\Controllers;

use App\Imagenesitem;
//use App\Palabrasclaveitem;
use App\Configuraciondato;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ImagenesitemsController extends Controller
{

    /**
     * Guarda las imagenes para un ítem
     */
    public function guardarImagenesItem(Request $request) {

        $itemId = $request['itemId'];
        $arrImagenes = $request['arrImagenes'];
        $parcialResp = true;
        
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // valida que se hayan enviado los datos del item y la ruta de la imagen
            if(!empty($itemId) && !empty($arrImagenes)) {

                $cont = 1;
                foreach($arrImagenes as $imagen) {
                    if($imagen == '') { continue; }

                    $data = array(
                        'url' => $imagen,
                        'item_id' => $itemId,
                        'posicion' => $cont,
                        'created_at' => date('Y-m-d H:i:s')
                    );

                    // Guarda la informacion de la imagen
                    $regId = Imagenesitem::guardarImagenesItem( $data );

                    $cont++;

                    if(empty($regId)){
                        $parcialResp = false;
                    }
                }

                // valida si el registro fue almacenado de forma correcta
                if( $parcialResp ) {
                    $resp['mensaje'] = 'Archivos cargados correctamente';
                    $resp['estado'] = true;
                } else {
                    $mensaje = 'No fue posible cargar la(s) imagen(es).';
                }
     
            } else {
                $resp['mensaje'] = 'Debe ingresar un ítem y una imagen';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }

    /**
     * Actualiza el estado de la imagen
     */
    public function actualizarEstado(Request $request) {
        $value = $request['value'];
        $imagenId = $request['idImage'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty($value) && !empty($imagenId) ) {

                // se obtienen las imagenes de los items 
                $rsp = Imagenesitem::actualizarEstado( $value, $imagenId ); 
                
                // valida si actualizó el estado
                if( $rsp ) {
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Estado actualizado de forma correcta.';
                } else {
                    $resp['mensaje'] = 'No fue posible actualizar el estado de la imagen';
                }

            } else {
                $resp['mensaje'] = 'Debe seleccionar una imagen y un estado.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }

    /**
     * Actualiza el estado que destaca en la imagen (ribbon)
     */
    public function actualizarEstadoImagen(Request $request) {
        $value = $request['value'];
        $imagenId = $request['idImage'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty($value) && !empty($imagenId) ) {

                // se obtienen las imagenes de los items 
                $rsp = Imagenesitem::actualizarEstadoImagen( $value, $imagenId ); 
                
                // valida si actualizó el registro
                if( $rsp ) {
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Estado actualizado de forma correcta.';
                } else {
                    $resp['mensaje'] = 'No fue posible actualizar el estado de la imagen.';
                }

            } else {
                $resp['mensaje'] = 'Debe seleccionar una imagen y un estado.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;  
    }

    /**
     * Elimina una imagen específica
     */
    public function eliminarImagenItem(Request $request) {
        $imagenId = $request['idImage'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty($imagenId) ) {

                // se obtienen las imagenes de los items 
                $rsp = Imagenesitem::eliminarImagenItem( $imagenId ); 
                
                // valida si actualizó el registro
                if( $rsp ) {
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'La imagen fue eliminada correctamente.';
                } else {
                    $resp['mensaje'] = 'No fue posible eliminar la imagen.';
                }

            } else {
                $resp['mensaje'] = 'Debe seleccionar una imagen.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;  
    }

    /**
     * Obtiene las imagenes relacionadas al item
     */
    public function obtenerImagenesItem(Request $request) {

        $itemId = $request['itemId'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {
        
            if( !empty( $itemId ) ) {

                // Se obtienen las imagenes de los items
                $imagenes = Imagenesitem::obtenerImagenesItem( $itemId ); 

                $imgArr = [];

                if( !empty( $imagenes['0']->id ) ) {

                    foreach($imagenes as $key => $img) {
                        $imgArr[$key]['id'] = $img->id;
                        $imgArr[$key]['url'] = $img->url;
                    }

                    $resp['estado'] = true;
                    $resp['data'] = $imgArr;
                } else {
                    $resp['mensaje'] = 'No fue posible almacenar la información de las imágenes.';
                }
            } else {
                $resp['mensaje'] = 'Debe seleccionar un producto para para cargar las imágenes.';
            }
        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }
        
        return $resp;

    }

    /**
     * Obtiene un producto especifico junto con un arreglo limitado de grupos relacionados por grupo y linea
     */
    public function obtenerDetallesItem(Request $request) {

        $idItem = $request['idItem'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        $urlDatax = Configuraciondato::obtenerConfiguracion('urldatax');

        $client = new Client();

        $response = $client->request('GET', $urlDatax['0']->valor . 'get-details-item/' . $idItem);

        if($response->getStatusCode() == '200') {            
            $content = (string) $response->getBody()->getContents();
            $productos = json_decode($content);

            if(!empty($productos->data)) {
                
                // se agregan las imagenes al item principal
                $productos->data->principal->imagenes = $this->obtenerImagenesProducto($idItem);

                // se agregan las imagenes a los productos relacionados por grupo
                foreach($productos->data->grupo as $key => $grp) {
                    $productos->data->grupo[$key]->imagenes = $this->obtenerImagenesProducto($grp->cod_item);
                }

                // se agregan las imagenes a los productos relacionados por linea
                foreach($productos->data->linea as $key => $lin) {
                    $productos->data->linea[$key]->imagenes =  $this->obtenerImagenesProducto($lin->cod_item);
                }

                $resp['mensaje'] = 'Se obtienen los productos de forma correcta.'; 
                $resp['estado'] = true;
                $resp['data'] = $productos; 
            } else {
                $resp['mensaje'] = $productos->mensaje;
            }

        } else {
            $resp['mensaje'] = 'No fue posible obtener resultados de Datax.';
        }
        
        return $resp;        
    }

    /**
     * Obtiene todos los items relacionados a un tipo de grupo
     */
    public function obtenerItemsPorGrupo(Request $request) {
        $codGru = $request['codGru'];

        $urlDatax = Configuraciondato::obtenerConfiguracion('urldatax');

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        $client = new Client();
        $response = $client->request('GET', $urlDatax['0']->valor . 'get-items-by-cod/' . $codGru);

        if($response->getStatusCode() == '200') {            
            $content = (string) $response->getBody()->getContents();
            $items = json_decode($content);

            foreach($items as $key => $producto) {
                $items[$key]->imagenes = $this->obtenerImagenesProducto($producto->cod_item);
            }    
        }

        if( !empty($items) ) {
            $resp['estado'] = true;
            $resp['data'] = $items;
        }

        return $resp;          

        echo $codGru;
    }

}