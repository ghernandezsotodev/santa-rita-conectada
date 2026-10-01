<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FcmController;
use App\Http\Controllers\Api\ComunicadoController;
use App\Http\Controllers\Api\EventoController;
use App\Http\Controllers\Api\DocumentoController;
use App\Http\Controllers\Api\ActaController;
use App\Http\Controllers\Api\AporteController;
use App\Http\Controllers\Api\SocioController;
use App\Http\Controllers\Api\TransaccionController;

// --- AUTENTICACIÓN (Pública) ---
Route::post('/login', [AuthController::class, 'login']);

// --- RUTAS PROTEGIDAS (Requieren Token) ---
Route::middleware('auth:sanctum')->group(function () {

    // --- GESTIÓN DE SESIÓN Y USUARIO ---
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/fcm-token', [FcmController::class, 'register']);

    // --- MÓDULOS DE LECTURA GENERAL ---
    Route::get('/comunicados', [ComunicadoController::class, 'index']);
    Route::get('/comunicados/{comunicado}', [ComunicadoController::class, 'show']);
    
    Route::get('/eventos', [EventoController::class, 'index']);
    Route::get('/eventos/{evento}', [EventoController::class, 'show']);
    
    Route::get('/documentos', [DocumentoController::class, 'index']);
    Route::get('/actas', [ActaController::class, 'index']);

    // --- MÓDULO PRIVADO DEL SOCIO ---
    Route::get('/aportes', [AporteController::class, 'index'])->middleware('role:Socio');
    Route::get('/charts/personal-finances', [AporteController::class, 'personalChart'])->middleware('role:Socio');

    // --- MÓDULO DE LA DIRECTIVA ---
    Route::get('/directivo/socios', [SocioController::class, 'index'])
        ->middleware('role:Presidente|Secretario|Tesorero');
        
    Route::get('/directivo/transacciones', [TransaccionController::class, 'index'])
        ->middleware('role:Presidente|Secretario|Tesorero');
        
    Route::post('/directivo/comunicados', [ComunicadoController::class, 'store'])
        ->middleware('role:Presidente|Secretario'); // Opcional: Recomendado proteger la creación
        
    Route::get('/charts/finances', [TransaccionController::class, 'financesChart'])
        ->middleware('role:Presidente|Tesorero');

    // --- TARJETAS DE RESUMEN (Dashboard de la Directiva) ---
    Route::get('/directivo/summary', function (
        App\Services\SocioService $socioService,
        App\Services\TransaccionService $transaccionService,
        App\Services\EventoService $eventoService
    ) {
        $balances = $transaccionService->getGlobalBalance();
        
        return response()->json([
            'total_socios' => $socioService->countAll(),
            'balance' => $balances['balance'],
            'comunicados_recientes' => App\Models\Comunicado::where('created_at', '>=', now()->subDays(30))->count(), 
            'proximos_eventos' => $eventoService->countUpcoming(),
        ]);
    })->middleware('role:Presidente|Secretario|Tesorero');

});