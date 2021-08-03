<?php

namespace App\Http\Controllers;

use App\CategoriasGrupo;
use App\Imagenesitem;
use App\Configuraciondato;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class CategoriasGruposController extends Controller
{
    
    /**
     * Retorna la información de todas las categorias registradas en la base de datos
     */
    public function obtenerGruposCategoria(Request $request)
    {
        $categoriaId = $request['categoriaId'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $categoriaId ) ) {

                // Obtiene la categoria
                $gruposCategoria = CategoriasGrupo::obtenerGruposCategoria( $categoriaId );

                if( $gruposCategoria ) {
                    $resp['estado'] = true;
                    $resp['data'] = $gruposCategoria;                            
                } else {
                    $resp['mensaje'] = 'No fue posible obtener los grupos de la categoria';
                }

            } else {
                $resp['mensaje'] = 'La información para obtener los grupos de la cateogoría no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp; 
    }

    /**
     * Obtiene la información completa de los grupos filtrados por categoria
     */
    public function obtenerInfoGruposCategoria(Request $request) {
        $categoriaId = $request['categoriaId'];

        $resp = array( 'estado' => false, 'data' => null, 'mensaje' => '' );

        try {

            if( !empty( $categoriaId ) ) {

                // Obtiene la categoria
                $gruposCategoria = CategoriasGrupo::obtenerGruposCategoria( $categoriaId );

                if(!empty($gruposCategoria['0'])) {

                    // Obtiene los codigos de los grupos de la categoria
                    $codsGrupos = [];
                    foreach ( $gruposCategoria as $val ) {                        
                        $codsGrupos[] = $val->tipo_gru;
                    }

                    $urlDatax = Configuraciondato::obtenerConfiguracion('urldatax');

                    $client = new Client();
                    $response = $client->request('GET', $urlDatax['0']->valor . 'get-info-groups/' . json_encode($codsGrupos));
    
                    if($response->getStatusCode() == '200') {            
                        $content = (string) $response->getBody()->getContents();
                        $grupos = json_decode($content);

                        $resp['estado'] = true;
                        $resp['data'] = $grupos;
                    }


                } else {
                    $resp['mensaje'] = 'No fue posible obtener la información de los grupos';
                }

            } else {
                $resp['mensaje'] = 'La información para obtener los grupos de la cateogoría no se encuentra completa.';
            }

        } catch(Throwable $e) {
            return array( 'estado' => false, 'data' => null, 'mensaje' => $e );
        }

        return $resp; 
    }
}