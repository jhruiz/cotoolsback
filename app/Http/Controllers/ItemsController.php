<?php

namespace App\Http\Controllers;

use App\Item;
use App\Imagenesitem;
use Illuminate\Http\Request;

class ItemsController extends Controller
{

    /**
     * Retorna la información de todos items registrados en la base de datos
     */    
    public function obtenerItems() {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // se obtienen los items
            $items = Item::obtenerItems();
            
            // valida si se econtraron registros
            if( !empty( $items ) ) {
                $resp['estado'] = true;
                $resp['data'] = $items;
            } else {
                $resp['mensaje'] = 'No se encontraron items';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;
    }

    /**
     * Obtiene la información de un item específico por id
     */
    public static function obtenerItem( Request $request ) {

        $itemId = isset($request['idItem']) ? $request['idItem'] : $request['itemId'];
        $take = 8;

        $resp = array( 'estado' => false, 'data' => null, 'imgItems' => null, 'imagenes' => null, 'grupo' => null, 'mensaje' => '' );

        try {

            if( !empty( $itemId ) ) {

                // se obtiene el item
                $items = Item::obtenerItem( $itemId );

                // se obtiene la imagen de un productos
                $imagenes = Imagenesitem::obtenerImagenesItem( $itemId );
                $imgProd = []; 
                if( isset( $imagenes['0']->url ) ) {
                    $imgProd[$itemId] = $imagenes['0']->url;
                }

                // se obtienen los items por grupo y se adjunta la primer imagen de cada uno
                $itemsGrp = Item::obtenerItemsPorGrupo( $items['0']->grupo_id, $take );
                if( !empty( $itemsGrp['0'] ) ) {
                    // se recorren los items para obtener las imágenes
                    foreach( $itemsGrp as $key => $val) {
                        $data = Imagenesitem::obtenerImagenesItem( $val->item_id );
        
                        if( isset( $data['0']->url ) ) {
                            $itemsGrp[$key]['url_imagen'] = $data['0']->url;
                        }
                    }
                }                
                
                // valida si se econtraron registros
                if( !empty( $items ) ) {
                    $resp['estado'] = true;
                    $resp['data'] = $items;
                    $resp['imgItems'] = $imgProd;
                    $resp['imagenes'] = $imagenes;
                    $resp['grupo'] = $itemsGrp;
                } else {
                    $resp['mensaje'] = 'No se encontró el producto.';
                }

            } else {
                $resp['mensaje'] = 'Debe seleccionar un producto para obtener su información.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'imagenes' => null, 'grupo' => null, 'imgItems' => null, 'mensaje' => $e );
        }

        return $resp;
    }

    /**
     * Obtener los productos que se usaran en el ecommerce
     */
    public function obtenerInfoProductos( Request $request ) {

        $pagina = $request['pagina'];
        $cantidad = $request['cantidad'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '', 'cantidad' => 0, 'imgItems' => null );

        try {

            // se calcula en cuanto inicia la consulta
            $skip = ($pagina * $cantidad) - $cantidad;

            // se obtienen la información de los items
            $items = Item::obtenerInfoProductos( $skip, $cantidad );

            // se obtiene la cantidad total de productos disponibles
            $cantTtal = Item::obtenerCantProds();

            // se obtienen las imagenes de los productos
            $imgItems = $this->obtenerImagenesItems( $items );

            // valida si se encontro el registro
            if( !empty( $items ) ) {

                $resp['estado'] = true;
                $resp['data'] = $items;
                $resp['cantidad'] = $cantTtal;
                $resp['imgItems'] = $imgItems;

            } else {
                $resp['mensaje'] = 'No se encontro informacion de productos';
            }

        } catch(Throwable $e) {

            return array( 'estado' => false, 'data' => null, 'mensaje' => $e, 'cantidad' => 0, 'imgItems' => null );
            
        }

        return $resp; 
    }

    /**
     * Obtiene las imagenes de los items a mostrar en el ecommerce
     */
    public function obtenerImagenesItems( $items ) {

        $arrImgs = [];

        if( !empty( $items ) ) {

            // se recorren los items para obtener las imágenes
            foreach( $items as $itm) {
                $data = Imagenesitem::obtenerImagenesItem( $itm->item_id );

                if( isset( $data['0']->url ) ) {
                    $arrImgs[$itm->item_id] = $data['0']->url;
                }
            }
        }

        return $arrImgs;
    }

    /**
     * Obtiene todos los items relacionados a una categoria específica
     */
    public function obtenerItemsPorGrupo( Request $request ) {

        $grupoId = $request['grupoId'];
        $take = 50;

        $resp = array( 'estado' => false, 'data' => null, 'imgItems' => null, 'mensaje' => '' );

        try {

            if( !empty( $grupoId ) ) {

                // se obtienen los items por grupo y se adjunta la primer imagen de cada uno
                $itemsGrp = Item::obtenerItemsPorGrupo( $grupoId, $take );

                // se obtiene y relacionan las imagenes de los items
                $imgItems = $this->obtenerImagenesItems($itemsGrp);
                
                // valida si se econtraron registros
                if( !empty( $itemsGrp['0'] ) ) {
                    $resp['estado'] = true;
                    $resp['data'] = $itemsGrp;
                    $resp['imgItems'] = $imgItems;
                } else {
                    $resp['mensaje'] = 'No se encontraron productos relacionados al grupo.';
                }

            } else {
                $resp['mensaje'] = 'Debe seleccionar un grupo para obtener su información.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'imgItems' => null, 'mensaje' => $e );
        }

        return $resp;
    }

    /**
     * Obtiene los items por palabras clave, codigo de barras y nombre del producto
     */
    public function buscarItems(Request $request) {
        $descripcion = $request['descripcion'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            // obtiene los productos por palabras clave
            $items = Item::obtenerInfoProductosPalabraClave( $descripcion );            

            if( !empty($items['0']->item_id) ) {

                // se obtienen las imagenes de los productos
                $imgItems = $this->obtenerImagenesItems( $items );                

                $resp['estado'] = true;
                $resp['data'] = $items;
                $resp['imgItems'] = $imgItems;
            } else {
                $resp['estado'] = false;
                $resp['mensaje'] = 'No se encontraron productos con la descripción.';    
            }
        }catch(Throwable $e) { 
            $resp['mensaje'] = 'No se encontraron productos con la descripción.';
        }

        return $resp;    
    }    
}