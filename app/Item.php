<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /**
     * Se obtiene un item por referencia
     */
    public static function obtenerItemPorReferencia( $referencia ) {
		  $data = Item::select()
                ->where('referencia', $referencia)
                ->get();
    	return $data;  
    }

    /**
     * Se obtiene un item por codigo
     */
    public static function obtenerItemPorCodigo( $codigo ) {
		  $data = Item::select()
                ->where('codigo', $codigo)
                ->get();
    	return $data;  
    }

    /**
     * Se obtienen todos los items registrados en la base de datos
     */
    public static function obtenerItems() {
      $data = Item::select( 'items.id as item_id', 'items.referencia', 'items.descripcion as item_desc',
                            'grupos.descripcion as grp_desc', 'lineas.descripcion as ln_desc')
                ->leftJoin('grupos', 'grupos.id', '=', 'items.grupo_id')
                ->leftJoin('unidadmedidas', 'unidadmedidas.id', '=', 'items.unidadmedida_id')
                ->leftJoin('itemstipos', 'itemstipos.id', '=', 'items.itemstipo_id')
                ->leftJoin('estadoitems', 'estadoitems.id', '=', 'items.estadoitem_id')
                ->leftJoin('lineas', 'lineas.id', '=', 'items.linea_id')
                ->get();

      return $data;
    }

    /**
     * Se obtiene la información de un item específico
     */
    public static function obtenerItem( $itemId ) {
      $data = Item::select( 'items.id as item_id', 'items.referencia', 'items.descripcion',
                            'items.desc_extensa', 'impuestos.id as impto_id', 'impuestos.descripcion as impto_desc', 
                            'impuestos.tasa as impto_tasa', 'grupos.descripcion as grp_desc', 'lineas.descripcion as ln_desc',
                            'listaprecios.precio1', 'listaprecios.ivaincp1', 'listaprecios.precio2',
                            'listaprecios.ivaincp2', 'listaprecios.precio3', 'listaprecios.ivaincp3',
                            'listaprecios.precio4', 'listaprecios.ivaincp4', 'saldos.saldoactual',
                            'items.codigo', 'items.unidad_factor', 'items.grupo_id')
                ->leftJoin('grupos', 'grupos.id', '=', 'items.grupo_id')
                ->leftJoin('unidadmedidas', 'unidadmedidas.id', '=', 'items.unidadmedida_id')
                ->leftJoin('itemstipos', 'itemstipos.id', '=', 'items.itemstipo_id')
                ->leftJoin('estadoitems', 'estadoitems.id', '=', 'items.estadoitem_id')
                ->leftJoin('lineas', 'lineas.id', '=', 'items.linea_id')
                ->leftJoin('impuestos', 'impuestos.id', '=', 'items.impuesto_id')
                ->leftJoin('saldos', 'saldos.item_id', '=', 'items.id')
                ->leftJoin('imagenesitems', 'imagenesitems.item_id', '=', 'items.id')
                ->leftjoin('listaprecios', 'listaprecios.item_id', '=', 'items.id')
                ->where('items.id', '=', $itemId)
                ->get();
      return $data;
    }

    /**
     * Se obtiene la información los items por grupo
     */
    public static function obtenerItemsPorGrupo( $grupoId, $take ) {
      $data = Item::select( 'items.id as item_id', 'items.referencia', 'items.descripcion',
                            'items.desc_extensa', 'impuestos.id as impto_id', 'impuestos.descripcion as impto_desc', 
                            'impuestos.tasa as impto_tasa', 'grupos.descripcion as grp_desc', 'lineas.descripcion as ln_desc',
                            'listaprecios.precio1', 'listaprecios.ivaincp1', 'listaprecios.precio2',
                            'listaprecios.ivaincp2', 'listaprecios.precio3', 'listaprecios.ivaincp3',
                            'listaprecios.precio4', 'listaprecios.ivaincp4', 'saldos.saldoactual',
                            'items.codigo', 'items.unidad_factor')
                ->leftJoin('grupos', 'grupos.id', '=', 'items.grupo_id')
                ->leftJoin('unidadmedidas', 'unidadmedidas.id', '=', 'items.unidadmedida_id')
                ->leftJoin('itemstipos', 'itemstipos.id', '=', 'items.itemstipo_id')
                ->leftJoin('estadoitems', 'estadoitems.id', '=', 'items.estadoitem_id')
                ->leftJoin('lineas', 'lineas.id', '=', 'items.linea_id')
                ->leftJoin('impuestos', 'impuestos.id', '=', 'items.impuesto_id')
                ->leftJoin('saldos', 'saldos.item_id', '=', 'items.id')
                ->leftJoin('imagenesitems', 'imagenesitems.item_id', '=', 'items.id')
                ->leftjoin('listaprecios', 'listaprecios.item_id', '=', 'items.id')
                ->where('items.grupo_id', '=', $grupoId)
                ->groupBy(  'items.id', 'listaprecios.precio1', 'listaprecios.ivaincp1', 
                            'listaprecios.precio2', 'listaprecios.ivaincp2', 'listaprecios.precio3', 
                            'listaprecios.ivaincp3', 'listaprecios.precio4', 'listaprecios.ivaincp4', 
                            'saldos.saldoactual', 'items.referencia', 'items.descripcion', 
                            'items.desc_extensa' )
                ->take($take)
                ->get();
      return $data;
    }

    /**
     * Retorna la cantidad de registros en la base de datos que cuentan con saldo
     */
    public static function obtenerCantProds() {
      $data = Item::select()
                      ->join('saldos', 'saldos.item_id', '=', 'items.id')
                      ->where('saldos.saldoactual', '>', 0)
                      ->count();
                      
      return $data;
    }

    /**
     * Obtiene la información completa de los productos registrados para el ecommerce
     */
    public static function obtenerInfoProductos($skip, $take) {

      $data = Item::select( 'items.id as item_id', 'items.referencia', 'items.descripcion',
                            'items.desc_extensa', 'impuestos.id as impto_id', 'impuestos.descripcion as impto_desc', 
                            'impuestos.tasa as impto_tasa', 'grupos.descripcion as grp_desc', 'lineas.descripcion as ln_desc',
                            'listaprecios.precio1', 'listaprecios.ivaincp1', 'listaprecios.precio2',
                            'listaprecios.ivaincp2', 'listaprecios.precio3', 'listaprecios.ivaincp3',
                            'listaprecios.precio4', 'listaprecios.ivaincp4', 'saldos.saldoactual',
                            'items.codigo')
                  ->leftjoin('listaprecios', 'listaprecios.item_id', '=', 'items.id')
                  ->join('saldos', 'saldos.item_id', '=', 'items.id')
                  ->leftjoin('impuestos', 'impuestos.id', '=', 'items.impuesto_id')
                  ->leftjoin('grupos', 'grupos.id', '=', 'items.grupo_id')
                  ->leftjoin('lineas', 'lineas.id', '=', 'items.linea_id')
                  ->where('saldos.saldoactual', '>', 0)
                  ->skip($skip)->take($take)
                  ->get();

      return $data;         
    }

    /**
     * Obtiene la información completa de los productos registrados para el ecommerce
     */
    public static function obtenerInfoProductosPalabraClave( $descripcion ) {

      $data = Item::select( 'items.id as item_id', 'items.referencia', 'items.descripcion',
                            'items.desc_extensa', 'impuestos.id as impto_id', 'impuestos.descripcion as impto_desc', 
                            'impuestos.tasa as impto_tasa', 'grupos.descripcion as grp_desc', 'lineas.descripcion as ln_desc',
                            'listaprecios.precio1', 'listaprecios.ivaincp1', 'listaprecios.precio2',
                            'listaprecios.ivaincp2', 'listaprecios.precio3', 'listaprecios.ivaincp3',
                            'listaprecios.precio4', 'listaprecios.ivaincp4', 'saldos.saldoactual',
                            'items.codigo')
                  ->leftjoin('listaprecios', 'listaprecios.item_id', '=', 'items.id')
                  ->join('saldos', 'saldos.item_id', '=', 'items.id')
                  ->leftjoin('impuestos', 'impuestos.id', '=', 'items.impuesto_id')
                  ->leftjoin('grupos', 'grupos.id', '=', 'items.grupo_id')
                  ->leftjoin('lineas', 'lineas.id', '=', 'items.linea_id')
                  ->leftjoin('palabrasclaves', 'palabrasclaves.item_id', '=', 'items.id')
                  ->where('saldos.saldoactual', '>', 0)
                  ->where('palabrasclaves.palabra', 'LIKE', '%' . $descripcion . '%')
                  ->orwhere('items.descripcion', 'LIKE', '%' . $descripcion . '%')
                  ->get();

      return $data;         
    }    
}