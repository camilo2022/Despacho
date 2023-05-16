<?php

use App\Models\QrDespachos;
use Illuminate\Support\Facades\Route;
use League\Flysystem\AdapterInterface;
use App\Models\User;


Route::get('/home',function(){
    return redirect('/');
});


Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    return "Cache is cleared";
});


Route::get('/yorgen',function(){
    return 'Hola mundo';
});







Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/register',function(){
    return redirect('/');
});
Route::prefix('filtro')->group(function () { 
    Route::get('WhatsApp/reports/iniciar',[App\Http\Controllers\FiltroController::class,'iniciarMessageWhatsAppReports'])->name('iniciarMessageWhatsAppReports');
    Route::get('WhatsApp/reports/enviar',[App\Http\Controllers\FiltroController::class,'enviarMessageWhatsAppReports'])->name('enviarMessageWhatsAppReports');

    Route::get('referencia',[App\Http\Controllers\FiltroController::class,'referencias'])->name('filtro.referencia');
    Route::get('referencia/consulta',[App\Http\Controllers\FiltroController::class,'getPedidosAndInventario'])->name('filtro.referencia.consulta');
    Route::post('referencia/store',[App\Http\Controllers\FiltroController::class,'storeDataFiltrada'])->name('filtro.referencia.store');
    Route::get('cargar',[App\Http\Controllers\FiltroController::class,'cargaBlessManufacturing'])->name('filtro.cargar');
    Route::post('cargar/store',[App\Http\Controllers\FiltroController::class,'storeCargaBlessManufacturing'])->name('filtro.cargar.store');

    Route::get('reporte/cliente',[App\Http\Controllers\FiltroController::class,'reporteCliente'])->name('filtro.reporte.cliente');
    Route::get('reporte/cliente/generar',[App\Http\Controllers\FiltroController::class,'generarReporteCliente'])->name('filtro.reporte.cliente.generar');
    Route::get('reporte/referencia',[App\Http\Controllers\FiltroController::class,'reporteReferencia'])->name('filtro.reporte.referencia');
    Route::get('reporte/referencia/generar',[App\Http\Controllers\FiltroController::class,'generarReporteReferencia'])->name('filtro.reporte.referencia.generar');
    Route::get('reporte/correria',[App\Http\Controllers\FiltroController::class,'reporteCorreria'])->name('filtro.reporte.correria');
    Route::get('reporte/correria/generar',[App\Http\Controllers\FiltroController::class,'generarReporteCorreria'])->name('filtro.reporte.correria.generar');
    Route::get('reporte/produccion',[App\Http\Controllers\FiltroController::class,'reporteProduccion'])->name('filtro.reporte.produccion');
    Route::get('reporte/produccion/generar',[App\Http\Controllers\FiltroController::class,'generarReporteProduccion'])->name('filtro.reporte.produccion.generar');

    Route::get('listado/ordenes/clientes',[App\Http\Controllers\FiltroController::class,'indexOrdenesClientes'])->name('filtro.listado.ordenes.clientes');
    Route::get('listado/ordenes/clientes/facturar/{id}',[App\Http\Controllers\FiltroController::class,'facturarOrdenesClientes'])->name('filtro.listado.ordenes.clientes.facturar');
    Route::post('listado/ordenes/clientes/facturar/{id}/add',[App\Http\Controllers\FiltroController::class,'addFacturasOrdenesClientes'])->name('filtro.listado.ordenes.clientes.facturar.add');
    
    Route::get('listado/ordenes/clientes/alistar/{id}',[App\Http\Controllers\FiltroController::class,'alistarOrdenesClientes'])->name('filtro.listado.ordenes.clientes.alistar');
    Route::get('listado/ordenes/clientes/alistar/picking/{id}',[App\Http\Controllers\FiltroController::class,'alistarPickingOrdenesClientes'])->name('filtro.listado.ordenes.clientes.alistar.picking');
    Route::post('listado/ordenes/clientes/alistar/picking/{id}/add',[App\Http\Controllers\FiltroController::class,'addAlistarPickingOrdenesClientes'])->name('filtro.listado.ordenes.clientes.alistar.picking.add');
    Route::post('listado/ordenes/clientes/alistar/picking/{id}/revisar',[App\Http\Controllers\FiltroController::class,'revisarAlistarPickingOrdenesClientes'])->name('filtro.listado.ordenes.clientes.alistar.picking.revisar');
    Route::post('listado/ordenes/clientes/alistar/picking/{id}/cancelar',[App\Http\Controllers\FiltroController::class,'cancelarAlistarPickingOrdenesClientes'])->name('filtro.listado.ordenes.clientes.alistar.picking.cancelar');
    Route::post('listado/ordenes/clientes/alistar/picking/{id}/aceptar',[App\Http\Controllers\FiltroController::class,'aceptarAlistarPickingOrdenesClientes'])->name('filtro.listado.ordenes.clientes.alistar.picking.aceptar');
    
    Route::get('listado/ordenes/clientes/empacar/{id}',[App\Http\Controllers\FiltroController::class,'empacarOrdenesClientes'])->name('filtro.listado.ordenes.clientes.empacar');
    Route::get('listado/ordenes/clientes/empacar/picking/{id}',[App\Http\Controllers\FiltroController::class,'empacarPickingOrdenesClientes'])->name('filtro.listado.ordenes.clientes.empacar.picking');
    Route::post('listado/ordenes/clientes/empacar/picking/{id}/crear',[App\Http\Controllers\FiltroController::class,'crearEmpacarPickingOrdenesClientes'])->name('filtro.listado.ordenes.clientes.empacar.picking.crear');
    Route::post('listado/ordenes/clientes/empacar/picking/{id}/add',[App\Http\Controllers\FiltroController::class,'addEmpacarPickingOrdenesClientes'])->name('filtro.listado.ordenes.clientes.empacar.picking.add');
    Route::post('listado/ordenes/clientes/empacar/picking/{id}/cancelar',[App\Http\Controllers\FiltroController::class,'cancelarEmpacarPickingOrdenesClientes'])->name('filtro.listado.ordenes.clientes.empacar.picking.cancelar');
    Route::post('listado/ordenes/clientes/empacar/picking/{id}/cerrar',[App\Http\Controllers\FiltroController::class,'cerrarEmpacarPickingOrdenesClientes'])->name('filtro.listado.ordenes.clientes.empacar.picking.cerrar');
    Route::post('listado/ordenes/clientes/empacar/picking/{id}/modificar',[App\Http\Controllers\FiltroController::class,'modificarEmpacarPickingOrdenesClientes'])->name('filtro.listado.ordenes.clientes.empacar.picking.modificar');
    Route::post('listado/ordenes/clientes/empacar/picking/{id}/finalizar',[App\Http\Controllers\FiltroController::class,'finalizarEmpacarPickingOrdenesClientes'])->name('filtro.listado.ordenes.clientes.empacar.picking.finalizar');

    Route::get('listado/ordenes/clientes/despachar/{id}',[App\Http\Controllers\FiltroController::class,'despacharOrdenesClientes'])->name('filtro.listado.ordenes.clientes.despachar');
    Route::get('listado/ordenes/clientes/cancelar/{id}',[App\Http\Controllers\FiltroController::class,'cancelarOrdenesClientes'])->name('filtro.listado.ordenes.clientes.cancelar');
    Route::get('listado/ordenes/clientes/reversar/{id}',[App\Http\Controllers\FiltroController::class,'reversarOrdenesClientes'])->name('filtro.listado.ordenes.clientes.reversar');
    Route::get('listado/ordenes/clientes/rotulos/{id}',[App\Http\Controllers\FiltroController::class,'rotulosOrdenesClientes'])->name('filtro.listado.ordenes.clientes.rotulos');
    
    Route::get('listado/ordenes/clientes/multiples/rotulos',[App\Http\Controllers\FiltroController::class,'rotulosMultiplesOrdenesClientes'])->name('filtro.listado.ordenes.clientes.rotulos.multiples');
    Route::post('listado/ordenes/clientes/multiples/rotulos/view',[App\Http\Controllers\FiltroController::class,'rotulosViewMultiplesOrdenesClientes'])->name('filtro.listado.ordenes.clientes.rotulos.multiples.view');
    
    Route::get('listado/ordenes/clientes/revisar/{consecutivo}',[App\Http\Controllers\FiltroController::class,'revisarOrdenesClientes'])->name('filtro.listado.ordenes.clientes.revisar');
    Route::post('listado/ordenes/clientes/revisar/{consecutivo}/{id}/aprobar',[App\Http\Controllers\FiltroController::class,'aprobarRevisionOrdenesClientes'])->name('filtro.listado.ordenes.clientes.revisar.aprobar');
    Route::post('listado/ordenes/clientes/revisar/{consecutivo}/{id}/cancelar',[App\Http\Controllers\FiltroController::class,'cancelarRevisionOrdenesClientes'])->name('filtro.listado.ordenes.clientes.revisar.cancelar');
    Route::get('listado/ordenes/clientes/view/{consecutivo}',[App\Http\Controllers\FiltroController::class,'viewOrdenesClientes'])->name('filtro.listado.ordenes.clientes.view');
    Route::get('listado/ordenes/clientes/print/{consecutivo}',[App\Http\Controllers\FiltroController::class,'printOrdenesClientes'])->name('filtro.listado.ordenes.clientes.print');
    Route::get('listado/ordenes/clientes/modificar/{consecutivo}/{amarrador}',[App\Http\Controllers\FiltroController::class,'editDetalleOrdenCliente'])->name('filtro.listado.ordenes.clientes.modificar.detalle');
    Route::post('listado/ordenes/clientes/update/{consecutivo}/{amarrador}/{id}',[App\Http\Controllers\FiltroController::class,'updateDetalleOrdenCliente'])->name('filtro.listado.ordenes.clientes.update.detalle');
    Route::get('listado/ordenes/clientes/estado/cancelado/{id}/{amarrador}',[App\Http\Controllers\FiltroController::class,'cancelarDetalleOrdenCliente'])->name('filtro.listado.ordenes.clientes.estado.cancelado');
    Route::get('listado/ordenes/clientes/estado/pendiente/{id}/{amarrador}',[App\Http\Controllers\FiltroController::class,'pendienteDetalleOrdenCliente'])->name('filtro.listado.ordenes.clientes.estado.pendiente');
    Route::get('listado/ordenes/clientes/estado/aprobado/{id}/{amarrador}',[App\Http\Controllers\FiltroController::class,'aprobarDetalleOrdenCliente'])->name('filtro.listado.ordenes.clientes.estado.aprobado');
    Route::get('listado/ordenes/print',[App\Http\Controllers\FiltroController::class,'printOrdenesDespacho'])->name('filtro.listado.ordenes.print');
    Route::get('listado/ordenes/print/actualizadas',[App\Http\Controllers\FiltroController::class,'printOrdenesDespachoActualizadas'])->name('filtro.listado.ordenes.actualizadas.print');
    Route::get('listado/ordenes/facturar/excel',[App\Http\Controllers\FiltroController::class,'excelDownloadOrdenesFacturar'])->name('filtro.listado.ordenes.facturar.excel');
    Route::get('excel',[App\Http\Controllers\FiltroController::class,'excelDownload'])->name('filtro.excel');
    Route::get('backup',[App\Http\Controllers\FiltroController::class,'backupDailyDataSystem'])->name('filtro.backupDailyDataSystem');
});

Route::prefix('filtro/medellin')->group(function () {
    Route::get('referencia',[App\Http\Controllers\FiltroMedellinController::class,'referencias'])->name('filtro.medellin.referencia');
    Route::get('referencia/consulta',[App\Http\Controllers\FiltroMedellinController::class,'getPedidosAndInventario'])->name('filtro.medellin.referencia.consulta');
    Route::post('referencia/store',[App\Http\Controllers\FiltroMedellinController::class,'storeDataFiltrada'])->name('filtro.medellin.referencia.store');
    Route::get('cargar',[App\Http\Controllers\FiltroMedellinController::class,'cargaBlessManufacturing'])->name('filtro.medellin.cargar');
    Route::post('cargar/store',[App\Http\Controllers\FiltroMedellinController::class,'storeCargaBlessManufacturing'])->name('filtro.medellin.cargar.store');

    Route::get('reporte/cliente',[App\Http\Controllers\FiltroMedellinController::class,'reporteCliente'])->name('filtro.medellin.reporte.cliente');
    Route::get('reporte/cliente/generar',[App\Http\Controllers\FiltroMedellinController::class,'generarReporteCliente'])->name('filtro.medellin.reporte.cliente.generar');
    Route::get('reporte/referencia',[App\Http\Controllers\FiltroMedellinController::class,'reporteReferencia'])->name('filtro.medellin.reporte.referencia');
    Route::get('reporte/referencia/generar',[App\Http\Controllers\FiltroMedellinController::class,'generarReporteReferencia'])->name('filtro.medellin.reporte.referencia.generar');
    Route::get('reporte/correria',[App\Http\Controllers\FiltroMedellinController::class,'reporteCorreria'])->name('filtro.medellin.reporte.correria');
    Route::get('reporte/correria/generar',[App\Http\Controllers\FiltroMedellinController::class,'generarReporteCorreria'])->name('filtro.medellin.reporte.correria.generar');
    Route::get('reporte/produccion',[App\Http\Controllers\FiltroMedellinController::class,'reporteProduccion'])->name('filtro.medellin.reporte.produccion');
    Route::get('reporte/produccion/generar',[App\Http\Controllers\FiltroMedellinController::class,'generarReporteProduccion'])->name('filtro.medellin.reporte.produccion.generar');
    
    Route::get('listado/ordenes/clientes',[App\Http\Controllers\FiltroMedellinController::class,'indexOrdenesClientes'])->name('filtro.medellin.listado.ordenes.clientes');
    Route::get('listado/ordenes/clientes/facturar/{id}',[App\Http\Controllers\FiltroMedellinController::class,'facturarOrdenesClientes'])->name('filtro.medellin.listado.ordenes.clientes.facturar');
    Route::get('listado/ordenes/clientes/alistar/{id}',[App\Http\Controllers\FiltroMedellinController::class,'alistarOrdenesClientes'])->name('filtro.medellin.listado.ordenes.clientes.alistar');
    Route::get('listado/ordenes/clientes/despachar/{id}',[App\Http\Controllers\FiltroMedellinController::class,'despacharOrdenesClientes'])->name('filtro.medellin.listado.ordenes.clientes.despachar');
    Route::get('listado/ordenes/clientes/cancelar/{id}',[App\Http\Controllers\FiltroMedellinController::class,'cancelarOrdenesClientes'])->name('filtro.medellin.listado.ordenes.clientes.cancelar');
    Route::get('listado/ordenes/clientes/reversar/{id}',[App\Http\Controllers\FiltroMedellinController::class,'reversarOrdenesClientes'])->name('filtro.medellin.listado.ordenes.clientes.reversar');
    Route::get('listado/ordenes/clientes/view/{consecutivo}',[App\Http\Controllers\FiltroMedellinController::class,'viewOrdenesClientes'])->name('filtro.medellin.listado.ordenes.clientes.view');
    Route::get('listado/ordenes/clientes/print/{consecutivo}',[App\Http\Controllers\FiltroMedellinController::class,'printOrdenesClientes'])->name('filtro.medellin.listado.ordenes.clientes.print');
    Route::get('listado/ordenes/clientes/edit/{consecutivo}/{amarrador}',[App\Http\Controllers\FiltroMedellinController::class,'editDetalleOrdenCliente'])->name('filtro.medellin.listado.ordenes.clientes.edit.detalle');
    Route::post('listado/ordenes/clientes/update/{consecutivo}/{amarrador}/{id}',[App\Http\Controllers\FiltroMedellinController::class,'updateDetalleOrdenCliente'])->name('filtro.medellin.listado.ordenes.clientes.update.detalle');
    Route::get('listado/ordenes/clientes/estado/cancelado/{id}/{amarrador}',[App\Http\Controllers\FiltroMedellinController::class,'cancelarDetalleOrdenCliente'])->name('filtro.medellin.listado.ordenes.clientes.estado.cancelado');
    Route::get('listado/ordenes/clientes/estado/pendiente/{id}/{amarrador}',[App\Http\Controllers\FiltroMedellinController::class,'pendienteDetalleOrdenCliente'])->name('filtro.medellin.listado.ordenes.clientes.estado.pendiente');
    Route::get('listado/ordenes/clientes/estado/aprobado/{id}/{amarrador}',[App\Http\Controllers\FiltroMedellinController::class,'aprobarDetalleOrdenCliente'])->name('filtro.medellin.listado.ordenes.clientes.estado.aprobado');
    Route::get('listado/ordenes/print',[App\Http\Controllers\FiltroMedellinController::class,'printOrdenesDespacho'])->name('filtro.medellin.listado.ordenes.print');
    Route::get('listado/ordenes/print/actualizadas',[App\Http\Controllers\FiltroMedellinController::class,'printOrdenesDespachoActualizadas'])->name('filtro.medellin.listado.ordenes.actualizadas.print');
    Route::get('listado/ordenes/facturar/excel',[App\Http\Controllers\FiltroMedellinController::class,'excelDownloadOrdenesFacturar'])->name('filtro.medellin.listado.ordenes.facturar.excel');
    Route::get('excel',[App\Http\Controllers\FiltroMedellinController::class,'excelDownload'])->name('filtro.medellin.excel');
    Route::get('inventario',[App\Http\Controllers\FiltroMedellinController::class,'reporteTotalDownload'])->name('filtro.medellin.reporte.total');
    Route::get('backup',[App\Http\Controllers\FiltroMedellinController::class,'backupDailyDataSystem'])->name('filtro.medellin.backupDailyDataSystem');
});

Route::prefix('user')->group(function () {
    Route::get('/all', [App\Http\Controllers\UserController::class, 'all']);
    Route::get('/mysolicitudes',[App\Http\Controllers\UserController::class, 'showMySolicitudes']);
    Route::get('/notificaciones',[App\Http\Controllers\UserController::class, 'getNotificaciones']);
    Route::get('/notificaciones-normal',[App\Http\Controllers\UserController::class, 'getNotificacionesNormal']);
    Route::get('/{id}',[App\Http\Controllers\UserController::class, 'show']);
});

Route::prefix('admin')->group(function () {
    Route::get('users', function () {
        return view('admin.users');
    })->name('admin.users'); 
    Route::get('/users/getUserByDocument/{documento}',[App\Http\Controllers\UserController::class, 'findByDocument']);
    Route::put('/users/create', [App\Http\Controllers\UserController::class, 'create']);
    Route::put('/users/edit', [App\Http\Controllers\UserController::class, 'edit']);
});

Route::prefix('rol')->group(function(){
    Route::get('/all',[App\Http\Controllers\RolController::class,'showAll']);
});
