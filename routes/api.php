<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api\ActaController;
use App\Http\Controllers\Api\AporteController;
use App\Http\Controllers\Api\ComunicadoController;
use App\Http\Controllers\Api\DocumentoController;
use App\Http\Controllers\Api\EventoController;
use App\Http\Controllers\Api\FcmController;
use App\Models\Socio;
use App\Models\Transaccion;
use Carbon\Carbon;
use App\Models\Comunicado;
use App\Models\Evento;
use Illuminate\Support\Facades\URL; 
use Illuminate\Support\Facades\Notification;
use App\Services\ComunicadoService;
use App\Services\SocioService;
use App\Http\Controllers\Api\TransaccionController;

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['Las credenciales proporcionadas son incorrectas.'],
        ]);
    }

    $token = $user->createToken($request->device_name)->plainTextToken;

    return response()->json(['token' => $token]);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        $user = $request->user();
        $user->load('roles');
        return $user;
    });

    Route::post('/fcm-token', [FcmController::class, 'register']);

    Route::get('/comunicados', [ComunicadoController::class, 'index']);
    Route::get('/comunicados/{comunicado}', [ComunicadoController::class, 'show']);

    Route::get('/eventos', [EventoController::class, 'index']);
    Route::get('/eventos/{evento}', [EventoController::class, 'show']);

    Route::get('/documentos', [DocumentoController::class, 'index']);
    Route::get('/actas', [ActaController::class, 'index']);

    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    });

    // Se mueve la ruta de aportes a su lugar correcto, fuera del closure de logout.
    Route::get('/aportes', [AporteController::class, 'index'])->middleware('role:Socio');


    // --- RUTA PARA LAS TARJETAS DE RESUMEN DE LA DIRECTIVA ---
    Route::get('/directivo/summary', function (
        App\Services\SocioService $socioService,
        App\Services\TransaccionService $transaccionService,
        App\Services\EventoService $eventoService
    ) {
        $balances = $transaccionService->getGlobalBalance();
        
        return response()->json([
            'total_socios' => $socioService->countAll(),
            'balance' => $balances['balance'],
            'comunicados_recientes' => App\Models\Comunicado::where('created_at', '>=', now()->subDays(30))->count(), // Deuda técnica final
            'proximos_eventos' => $eventoService->countUpcoming(),
        ]);
    })->middleware('role:Presidente|Secretario|Tesorero');

    // --- RUTA PARA LA LISTA DE SOCIOS (APP MÓVIL) ---
    Route::get('/directivo/socios', [SocioController::class, 'index'])->middleware('role:Presidente|Secretario|Tesorero');

    // --- RUTA PARA EL HISTORIAL DE TESORERÍA (APP MÓVIL) ---
    Route::get('/directivo/transacciones', [TransaccionController::class, 'index'])->middleware('role:Presidente|Secretario|Tesorero');

    // --- RUTA PARA CREAR UN COMUNICADO (APP MÓVIL) ---
    Route::post('/directivo/comunicados', [ComunicadoController::class, 'store']);

    // --- RUTA PARA EL GRÁFICO DE LA DIRECTIVA ---
    Route::get('/charts/finances', [TransaccionController::class, 'financesChart'])->middleware('role:Presidente|Tesorero');

    // --- RUTA PARA EL GRÁFICO PERSONAL DEL SOCIO ---
    Route::get('/charts/personal-finances', [AporteController::class, 'personalChart'])->middleware('role:Socio');

});