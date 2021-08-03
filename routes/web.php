<?php

Route::get('enviarcorreo', 'App\Http\Controllers\UsuariosController@enviarCorreo');

// Rutas para usuarios
Route::get('get-users', 'App\Http\Controllers\UsuariosController@obtenerUsuarios');
Route::get('get-user', 'App\Http\Controllers\UsuariosController@obtenerUsuario');
Route::get('create-user', 'App\Http\Controllers\UsuariosController@crearUsuario');
Route::get('update-user', 'App\Http\Controllers\UsuariosController@actualizarUsuario');
Route::get('login-user', 'App\Http\Controllers\UsuariosController@loginUsuario');
Route::get('sincronize-users', 'App\Http\Controllers\UsuariosController@obtenerUsuariosDatax');

// Rutas para perfiles
Route::get('get-profiles', 'App\Http\Controllers\PerfilesController@obtenerPerfiles');

// Rutas para estados
Route::get('get-states', 'App\Http\Controllers\EstadosController@obtenerEstados');

// Rutas para los estados de los items
Route::get('get-items-states', 'App\Http\Controllers\EstadositemsController@obtenerEstadosItems');

// Rutas para documentos
Route::get('get-documents', 'App\Http\Controllers\DocumentosController@obtenerDocumentos');

// Rutas para documentos de usuarios
Route::get('get-user-documents', 'App\Http\Controllers\DocumentosUsuariosController@obtenerDocumentosUsuario');
Route::get('save-user-documents', 'App\Http\Controllers\DocumentosUsuariosController@crearDocumentosUsuario');
Route::get('check-user-documents', 'App\Http\Controllers\DocumentosUsuariosController@verficarDocumento');

// Rutas para los items
Route::get('get-items', 'App\Http\Controllers\ImagenesitemsController@obtenerItems');
Route::get('get-item-detail', 'App\Http\Controllers\ImagenesitemsController@obtenerDetallesItem');
Route::get('get-items-name', 'App\Http\Controllers\ImagenesitemsController@obtenerItemsGeneral');
Route::get('get-items-group', 'App\Http\Controllers\ImagenesitemsController@obtenerItemsPorGrupo');

// Rutas para los pedidos
Route::get('get-orders', 'App\Http\Controllers\PedidosController@obtenerPedidos');
Route::get('update-order/{id}', 'App\Http\Controllers\PedidosController@actualizarEstadoPago');
Route::get('add-items-to-car', 'App\Http\Controllers\PedidosController@guardarPedido');
Route::get('get-order-detail', 'App\Http\Controllers\PedidosController@obtenerDetallePedido');
Route::get('validate-order', 'App\Http\Controllers\PedidosController@validarPedido');
Route::get('update-units-order', 'App\Http\Controllers\PedidosController@actualizarUnidadesPedido');
Route::get('get-simple-info-order/{userId}', 'App\Http\Controllers\PedidosController@obtenerPedidoSimple');
Route::get('approve-order', 'App\Http\Controllers\PedidosController@aprobarPedido');
Route::get('get-orders-client', 'App\Http\Controllers\PedidosController@obtenerPedidosCliente');
Route::get('update-order-state', 'App\Http\Controllers\PedidosController@actualizarEstadoPedido');
Route::get('update-url-guide', 'App\Http\Controllers\PedidosController@actualizarUrlGuia');

// Rutas para detalle de los pedidos
Route::get('get-order-details', 'App\Http\Controllers\PedidosdetallesController@obtenerPedidoDetalles');
Route::get('save-order-detail/{pedidoId}/{codItem}/{cant}/{precioVentaU}/{vlrIva}', 'App\Http\Controllers\PedidosdetallesController@guardarDetallePedido');
Route::get('delete-item', 'App\Http\Controllers\PedidosdetallesController@eliminarItemPedido');
Route::get('change-cant-item', 'App\Http\Controllers\PedidosdetallesController@cambiarCantidadItem');

// Rutas para palabras clave
Route::get('save-key-word', 'App\Http\Controllers\PalabrasclaveitemsController@crearPalabraClaveItem');
Route::get('get-key-word', 'App\Http\Controllers\PalabrasclaveitemsController@obtenerPalabrasClaveItem');
Route::get('delete-key-word', 'App\Http\Controllers\PalabrasclaveitemsController@eliminarPalabrasClaveItem');

// Rutas para imagenes
Route::get('save-images', 'App\Http\Controllers\ImagenesitemsController@guardarImagenesItem');
Route::get('get-images', 'App\Http\Controllers\ImagenesitemsController@obtenerImagenesItem');
Route::get('change-state', 'App\Http\Controllers\ImagenesitemsController@actualizarEstado');
Route::get('change-state-image', 'App\Http\Controllers\ImagenesitemsController@actualizarEstadoImagen');
Route::get('change-position-image', 'App\Http\Controllers\ImagenesitemsController@actualizarPosicionImagen');
Route::get('delete-image-item', 'App\Http\Controllers\ImagenesitemsController@eliminarImagenItem');

// Rutas para las categorias
Route::get('get-categories', 'App\Http\Controllers\CategoriasController@obtenerCategorias');
Route::get('save-category', 'App\Http\Controllers\CategoriasController@guardarCategoria');
Route::get('get-category', 'App\Http\Controllers\CategoriasController@obtenerCategoria');
Route::get('update-category', 'App\Http\Controllers\CategoriasController@actualizarCategoria');

// Rutas para los grupos de las categorias
Route::get('get-groups-category', 'App\Http\Controllers\CategoriasGruposController@obtenerGruposCategoria');
Route::get('get-info-groups-category', 'App\Http\Controllers\CategoriasGruposController@obtenerInfoGruposCategoria');

// Rutas para los estados de los pedidos
Route::get('get-status-order', 'App\Http\Controllers\EstadospedidosController@obtenerEstadospedidos');
