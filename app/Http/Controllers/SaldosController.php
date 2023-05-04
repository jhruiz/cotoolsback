<?php

namespace App\Http\Controllers;

use App\Saldo;
use App\Item;
use Illuminate\Http\Request;

class SaldosController extends Controller
{
    
    /**
     * Retorna la información de todos los saldos registrados en la base de datos
     */
    public function obtenerSaldos( $itemId )
    {

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        if(!empty( $itemId )) {
            try {
    
                // se obtienen saldos de un item particular
                $saldos = Saldo::obtenerSaldos( $itemId );
                
                // valida si se econtraron registros
                if( !empty( $saldos ) ) {
                    $resp['estado'] = true;
                    $resp['data'] = $saldos;
                } else {
                    $resp['mensaje'] = 'No se encontraron saldos para el producto';
                }
    
            } catch(Throwable $e) {
                return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
            }
        } else {
            $resp['mensaje'] = 'Debe seleccionar un producto para obtener la lista de precios correspondiente.';
        }

        return $resp;

    }

    /**
     * Funcion para guardar un departamento
     */
    public function crearSaldo( Request $request ) {
        
        $item_id = $request['item_id'];
        $codigobodega = $request['codigobodega'];
        $saldoactual = $request['saldoactual'];
        $saldopedidos = $request['saldopedidos'];
        
        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {
          
            if( !empty( $item_id ) && !empty( $codigobodega ) && !empty( $saldoactual ) && !empty( $saldopedidos )) {

                $data = array(
                    'item_id' => $item_id,
                    'codigobodega' => $codigobodega,
                    'saldoactual' => $saldoactual,
                    'saldopedidos' => $saldopedidos,
                    'created_at' => date('Y-m-d H:i:s')
                );

                // Crea la linea
                $id = Saldo::crearSaldo( $data );

                if( $id ) {                        
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Saldo creado correctamente.';
                    $resp['data'] = $id;                            
                } else {
                    $resp['mensaje'] = 'No fue posible crear el saldo';
                }

            } else {
                $resp['mensaje'] = 'La información para creación del saldo no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp;        
    }

    /**
     * Funcion que actualiza la información de un saldo específico
     */
    public function actualizarSaldo( Request $request ) {
        
        $referencia = $request['referencia'];
        $codigobodega = $request['codigobodega'];
        $saldoactual = $request['saldoactual'];
        $saldopedidos = $request['saldopedidos'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $referencia ) && !empty( $codigobodega ) && !empty( $saldoactual ) && !empty( $saldopedidos ) ) {

                // Obtiene la informacion del item
                $itemId = Item::obtenerItemPorReferencia( $referencia )['0']->id;

                // Actualiza los saldos de un item
                $respCat = Saldo::actualizarSaldo( $itemId, $codigobodega, $saldoactual, $saldopedidos );

                if( $respCat ) {
                    $resp['estado'] = true;
                    $resp['mensaje'] = 'Saldos actualizados correctamente.';
                    $resp['data'] = null;                            
                } else {
                    $resp['mensaje'] = 'No fue posible actualizar los saldos del item.';
                }

            } else {
                $resp['mensaje'] = 'La información para la actualización del saldo no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp; 
    }

}