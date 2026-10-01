<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TransaccionService;

class AporteController extends Controller
{
    protected TransaccionService $transaccionService;

    public function __construct(TransaccionService $transaccionService)
    {
        $this->transaccionService = $transaccionService;
    }

    public function index(Request $request)
    {
        $socio = $request->user()->socio;

        if (!$socio) {
            return response()->json(['message' => 'No se encontró un perfil de socio asociado.'], 404);
        }

        $balances = $this->transaccionService->getPersonalBalance($socio->id);
        $transacciones = $this->transaccionService->getPaginatedBySocioWithSignedUrls($socio->id, 20);

        return response()->json([
            'balance_personal' => $balances['balance'],
            'transacciones' => $transacciones,
        ]);
    }

    public function personalChart(Request $request)
    {
        $socio = $request->user()->socio;

        if (!$socio) {
            return response()->json(['labels' => [], 'datasets' => []], 404);
        }

        return response()->json($this->transaccionService->getPersonalChartData($socio->id));
    }
}
