<?php

// Ruta de prueba que envia un correo
Route::get('enviarcorreo', 'App\Http\Controllers\UsuariosController@enviarCorreo');
Route::get('enviarcorreopedido/{userId}/{pdweb}', 'App\Http\Controllers\PedidosController@enviarCorreoPedido');

// Rutas para usuarios
Route::get('usuarios/obtener', 'App\Http\Controllers\UsuariosController@obtenerUsuarios'); //get-users
Route::get('usuario/obtener', 'App\Http\Controllers\UsuariosController@obtenerUsuario'); //get-user
Route::get('usuario/autoactualiza', 'App\Http\Controllers\UsuariosController@selfUpdateUser'); //get-user
Route::get('usuarios/crearsinc', 'App\Http\Controllers\UsuariosController@crearUsuarioSinc'); //create-user
Route::get('usuarios/crear', 'App\Http\Controllers\UsuariosController@crearUsuario'); //create-user
Route::get('usuario/actualizar', 'App\Http\Controllers\UsuariosController@actualizarUsuario'); //update-user
Route::get('usuario/login', 'App\Http\Controllers\UsuariosController@loginUsuario'); //login-user

// Rutas para perfiles
Route::get('perfiles/obtener', 'App\Http\Controllers\PerfilesController@obtenerPerfiles'); //get-profiles

// Rutas para estados
Route::get('estados/obtener', 'App\Http\Controllers\EstadosController@obtenerEstados'); //get-states

// Rutas para los estados de los items
Route::get('estadoitems/obtener', 'App\Http\Controllers\EstadoitemsController@obtenerEstadosItems'); //get-items-states

// Rutas para documentos
Route::get('documentos/obtener', 'App\Http\Controllers\DocumentosController@obtenerDocumentos'); //get-documents

// Rutas para documentos de usuarios
Route::get('documentosusr/obtener', 'App\Http\Controllers\DocumentosUsuariosController@obtenerDocumentosUsuario'); //get-user-documents
Route::get('documentosusr/crear', 'App\Http\Controllers\DocumentosUsuariosController@crearDocumentosUsuario'); //save-user-documents
Route::get('documentosusr/check', 'App\Http\Controllers\DocumentosUsuariosController@verficarDocumento'); //check-user-documents

// Rutas para los items
Route::get('items/obtener', 'App\Http\Controllers\ItemsController@obtenerItems');
Route::get('item/obtener', 'App\Http\Controllers\ItemsController@obtenerItem'); //get-item-detail
Route::get('itemsecommerce/obtener', 'App\Http\Controllers\ItemsController@obtenerInfoProductos'); //get-item-detail
Route::get('items/buscaritem', 'App\Http\Controllers\ItemsController@buscarItems'); //get-items-general
Route::get('itemsgrupo/obtener', 'App\Http\Controllers\ItemsController@obtenerItemsPorGrupo'); //get-items-group

// Rutas para los pedidos
Route::get('pedido/obtenerpedidos', 'App\Http\Controllers\PedidosController@obtenerPedidos'); // get-orders
Route::get('update-order/{id}', 'App\Http\Controllers\PedidosController@actualizarEstadoPago');
Route::get('pedido/agregaritem', 'App\Http\Controllers\PedidosController@guardarPedido');
Route::get('pedido/cantidaditems', 'App\Http\Controllers\PedidosController@obtenerCantidadItems');
Route::get('pedido/validarorden', 'App\Http\Controllers\PedidosController@validarPedido'); // validate-order
Route::get('pedido/actualizarunidades', 'App\Http\Controllers\PedidosController@actualizarUnidadesPedido'); // update-units-order
Route::get('get-simple-info-order/{userId}', 'App\Http\Controllers\PedidosController@obtenerPedidoSimple');
Route::get('pedido/aprobarpedido', 'App\Http\Controllers\PedidosController@aprobarPedido'); // approve-order
Route::get('pedido/obtenerpedidocliente', 'App\Http\Controllers\PedidosController@obtenerPedidosCliente'); // get-orders-client
Route::get('pedido/actualizarestado', 'App\Http\Controllers\PedidosController@actualizarEstadoPedido'); // update-order-state
Route::get('pedido/actualizarguia', 'App\Http\Controllers\PedidosController@actualizarUrlGuia'); // update-url-guide
Route::get('pedido/sincronizarpedido', 'App\Http\Controllers\PedidosController@sincronizarpedido'); // update-url-guide
Route::get('pedido/detallepedido', 'App\Http\Controllers\PedidosController@obtenerDetallePedido'); // update-url-guide

// Rutas para detalle de los pedidos
Route::get('pedidodetalle/obtenerdetalle', 'App\Http\Controllers\PedidosdetallesController@obtenerPedidoDetalles'); // get-order-details
Route::get('save-order-detail/{pedidoId}/{codItem}/{cant}/{precioVentaU}/{vlrIva}', 'App\Http\Controllers\PedidosdetallesController@guardarDetallePedido');
Route::get('pedidodetalle/eliminaritem', 'App\Http\Controllers\PedidosdetallesController@eliminarItemPedido'); // delete-item
Route::get('pedidodetalle/cambiarcantidad', 'App\Http\Controllers\PedidosdetallesController@cambiarCantidadItem'); // change-cant-item

// Rutas para imagenes
Route::get('imagenes/guardar', 'App\Http\Controllers\ImagenesitemsController@guardarImagenesItem');
Route::get('imagenesitem/obtener', 'App\Http\Controllers\ImagenesitemsController@obtenerImagenesItem');
Route::get('change-state', 'App\Http\Controllers\ImagenesitemsController@actualizarEstado');
Route::get('change-state-image', 'App\Http\Controllers\ImagenesitemsController@actualizarEstadoImagen');
Route::get('change-position-image', 'App\Http\Controllers\ImagenesitemsController@actualizarPosicionImagen');
Route::get('imagenes/eliminar', 'App\Http\Controllers\ImagenesitemsController@eliminarImagenItem');

// Rutas para las categorias
Route::get('categorias/obtener', 'App\Http\Controllers\CategoriasController@obtenerCategorias'); //get-categories
Route::get('categorias/guardar', 'App\Http\Controllers\CategoriasController@guardarCategoria'); //save-category
Route::get('categoria/obtener', 'App\Http\Controllers\CategoriasController@obtenerCategoria'); //get-category
Route::get('categoria/actualizar', 'App\Http\Controllers\CategoriasController@actualizarCategoria'); //update-category

// Rutas para los grupos de las categorias
Route::get('gruposcategorias/obtener', 'App\Http\Controllers\CategoriasGruposController@obtenerGruposCategoria');

// Rutas para los estados de los pedidos
Route::get('estadopedidos/obtener', 'App\Http\Controllers\EstadopedidosController@obtenerEstadospedidos'); //get-status-order

////////////////////////////////////////////////////////////////////////////////////////////////////////
// Rutas para las ciudades
Route::get('ciudades/crear', 'App\Http\Controllers\CiudadesController@crearCiudad');
Route::get('ciudades/obtener/{dptoId?}', 'App\Http\Controllers\CiudadesController@obtenerCiudades');
Route::get('ciudades/actualizar', 'App\Http\Controllers\CiudadesController@actualizarCiudad');

// Rutas para los departamentos
Route::get('departamentos/crear', 'App\Http\Controllers\DepartamentosController@crearDepartamento');
Route::get('departamentos/obtener', 'App\Http\Controllers\DepartamentosController@obtenerDepartamentos');
Route::get('departamentos/actualizar', 'App\Http\Controllers\DepartamentosController@actualizarDepartamento');

// Rutas para los grupos
Route::get('grupos/crear', 'App\Http\Controllers\GruposController@crearGrupo');
Route::get('grupos/obtener', 'App\Http\Controllers\GruposController@obtenerGrupos');
Route::get('grupos/actualizar', 'App\Http\Controllers\GruposController@actualizarGrupos');

// Rutas para los impuestos
Route::get('impuestos/crear', 'App\Http\Controllers\ImpuestosController@crearImpuesto');
Route::get('impuestos/obtener', 'App\Http\Controllers\ImpuestosController@obtenerImpuestos');
Route::get('impuestos/actualizar', 'App\Http\Controllers\ImpuestosController@actualizarImpuesto');

// Rutas para los tipos de items
Route::get('itemstipos/crear', 'App\Http\Controllers\ItemstiposController@crearItemstipo');
Route::get('itemstipos/obtener', 'App\Http\Controllers\ItemstiposController@obtenerItemstipos');
Route::get('itemstipos/actualizar', 'App\Http\Controllers\ItemstiposController@actualizarItemstipo');

// Rutas para las lineas de los productos
Route::get('lineas/crear', 'App\Http\Controllers\LineasController@crearLinea');
Route::get('lineas/obtener', 'App\Http\Controllers\LineasController@obtenerLineas');
Route::get('lineas/actualizar', 'App\Http\Controllers\LineasController@actualizarLinea');

// Rutas para los productos
Route::get('paises/obtener', 'App\Http\Controllers\PaisesController@obtenerPaises');

// Rutas para los regimen
Route::get('regimen/crear', 'App\Http\Controllers\RegimenesController@crearRegimen');
Route::get('regimen/obtener', 'App\Http\Controllers\RegimenesController@obtenerRegimen');
Route::get('regimen/actualizar', 'App\Http\Controllers\RegimenesController@actualizarRegimen');

// Rutas para los tipos de documentos
Route::get('tipodocumentos/crear', 'App\Http\Controllers\TipodocumentosController@crearTipodocumento');
Route::get('tipodocumentos/obtener', 'App\Http\Controllers\TipodocumentosController@obtenerTiposdocumentos');
Route::get('tipodocumentos/actualizar', 'App\Http\Controllers\TipodocumentosController@actualizarTipodocumento');

// Rutas para los tipos de impuestos
Route::get('tipoimpuestos/crear', 'App\Http\Controllers\TipoimpuestosController@crearTipoimpuesto');
Route::get('tipoimpuestos/obtener', 'App\Http\Controllers\TipoimpuestosController@obtenerTiposimpuestos');
Route::get('tipoimpuestos/actualizar', 'App\Http\Controllers\TipoimpuestosController@actualizarTipoimpuesto');

// Rutas para los tipos de personas
Route::get('tipopersonas/crear', 'App\Http\Controllers\TipopersonasController@crearTipopersona');
Route::get('tipopersonas/obtener', 'App\Http\Controllers\TipopersonasController@obtenerTipospersonas');
Route::get('tipopersonas/actualizar', 'App\Http\Controllers\TipopersonasController@actualizarTipopersona');

// Rutas para las unidades de medida
Route::get('unidadmedidas/crear', 'App\Http\Controllers\UnidadmedidasController@crearUnidadmedida');
Route::get('unidadmedidas/obtener', 'App\Http\Controllers\UnidadmedidasController@obtenerUnidadmedidas');
Route::get('unidadmedidas/actualizar', 'App\Http\Controllers\UnidadmedidasController@actualizarUnidadmedida');

// Rutas para las clases de los grupos
Route::get('clasegrupo/obtener', 'App\Http\Controllers\ClasegruposController@obtenerClasesgrupos');

// Rutas para los tipos de pagos
Route::get('tipopagos/obtener', 'App\Http\Controllers\TipopagosController@obtenerClasesgrupos');

// Rutas para las listas de precios
Route::get('listaprecios/obtener/{itemId}', 'App\Http\Controllers\ListapreciosController@obtenerListasPrecios');
Route::get('listaprecios/crear', 'App\Http\Controllers\ListapreciosController@crearListaPrecios');
Route::get('listaprecios/actualizar', 'App\Http\Controllers\ListapreciosController@actualizarListaPrecio');

// Rutas para los saldos
Route::get('saldos/obtener/{itemId}', 'App\Http\Controllers\SaldosController@obtenerSaldos');
Route::get('saldos/crear', 'App\Http\Controllers\SaldosController@crearSaldo');
Route::get('saldos/actualizar', 'App\Http\Controllers\SaldosController@actualizarSaldo');

// Rutas para las palabras clave
Route::get('palabrasclave/obtener', 'App\Http\Controllers\PalabrasclavesController@obtenerPalabrasClaveItem');
Route::get('palabrasclave/guardar', 'App\Http\Controllers\PalabrasclavesController@crearPalabraClaveItem');
Route::get('palabrasclave/eliminar', 'App\Http\Controllers\PalabrasclavesController@eliminarPalabrasClaveItem');

// Rutas webhooks para mercadopago
Route::post('webhooks', 'App\Http\Controllers\PedidosController@whmercadopago');

