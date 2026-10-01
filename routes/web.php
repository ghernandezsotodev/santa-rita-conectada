<?php

use App\Http\Controllers\Portal\ComunicadoController as PortalComunicadoController;
use App\Http\Controllers\Portal\EventoController as PortalEventoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocioController;
use App\Http\Controllers\ActaController;
use App\Http\Controllers\ComunicadoController;
use App\Http\Controllers\SubsidioController;
use App\Http\Controllers\TransaccionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\DocumentoController;
use App\Exports\TransaccionesExport;
use App\Models\Documento;
use App\Models\Acta;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Portal\AporteController as PortalAporteController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// --- GRUPO DE RUTAS PROTEGIDAS POR AUTENTICACIÓN (DIRECTIVA) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('socios', SocioController::class)->middleware('role:Secretario|Presidente');
    
    Route::resource('actas', ActaController::class)->middleware('role:Secretario|Presidente|Tesorero');
    Route::resource('comunicados', ComunicadoController::class)->middleware('role:Secretario|Presidente|Tesorero');
    Route::post('/comunicados/{comunicado}/enviar', [ComunicadoController::class, 'enviar'])->name('comunicados.enviar')->middleware('role:Secretario|Presidente|Tesorero');
    
    Route::get('/transacciones/exportar', function () {
        return Excel::download(new TransaccionesExport, 'balance-tesoreria.xlsx');
    })->name('transacciones.exportar')->middleware('role:Tesorero|Presidente');
    Route::resource('transacciones', TransaccionController::class)->parameters(['transacciones' => 'transaccion'])->middleware('role:Tesorero|Presidente');
    Route::resource('subsidios', SubsidioController::class)->middleware('role:Presidente|Secretario|Tesorero');
    Route::resource('eventos', EventoController::class)->middleware('role:Secretario|Presidente|Tesorero');
    Route::resource('documentos', DocumentoController::class)->middleware('role:Presidente|Secretario|Tesorero');
});

// --- RUTAS DEL PORTAL PARA SOCIOS ---
Route::middleware(['auth', 'role:Socio', 'password.changed'])->prefix('portal')->name('portal.')->group(function () {

    Route::get('/documentos', function () {
        $documentos = Documento::latest()->paginate(10);
        return view('portal.documentos.index', compact('documentos'));
    })->name('documentos.index');

    Route::get('/documentos/{documento}', function (Documento $documento) {
        return view('portal.documentos.show', compact('documento'));
    })->name('documentos.show');

    // Ruta de descarga para el portal web (usa sesión)
    Route::get('/documentos/{documento}/descargar', [DocumentoController::class, 'show'])
         ->name('documentos.descargar');

    Route::get('/actas', [App\Http\Controllers\Portal\ActaController::class, 'index'])->name('actas.index');

    Route::get('/actas/{acta}', [App\Http\Controllers\Portal\ActaController::class, 'show'])->name('actas.show');

    // Ruta de descarga para el portal web (usa sesión)
    Route::get('/actas/{acta}/descargar', [ActaController::class, 'descargarParaSocio'])
         ->name('actas.descargar');

    Route::get('/comunicados', [PortalComunicadoController::class, 'index'])->name('comunicados.index');
    Route::get('/comunicados/{comunicado}', [PortalComunicadoController::class, 'show'])->name('comunicados.show');

    Route::get('/eventos', [PortalEventoController::class, 'index'])->name('eventos.index');
    Route::get('/eventos/{evento}', [PortalEventoController::class, 'show'])->name('eventos.show');

    Route::get('/aportes', [PortalAporteController::class, 'index'])->name('aportes.index');

    Route::get('/transacciones/{transaccion}/comprobante', [PortalAporteController::class, 'descargarComprobante'])
         ->name('comprobantes.descargar');

});

// --- RUTAS FIRMADAS PARA DESCARGAS DESDE ANDROID (PÚBLICAS PERO SEGURAS) ---
// Estas rutas validan la firma criptográfica en la URL. No piden login.

Route::get('/descargas/publicas/comprobante/{transaccion}', [PortalAporteController::class, 'descargarPublico'])
    ->name('comprobantes.publico')
    ->middleware('signed');

// NUEVA: Ruta firmada para Actas
Route::get('/descargas/publicas/acta/{acta}', [ActaController::class, 'descargarPublico'])
    ->name('actas.publico')
    ->middleware('signed');

// NUEVA: Ruta firmada para Documentos
Route::get('/descargas/publicas/documento/{documento}', [DocumentoController::class, 'descargarPublico'])
    ->name('documentos.publico')
    ->middleware('signed');

require __DIR__.'/auth.php';