<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comunicado;
use App\Services\ComunicadoService;

class ComunicadoController extends Controller
{
    protected ComunicadoService $comunicadoService;

    public function __construct(ComunicadoService $comunicadoService)
    {
        $this->comunicadoService = $comunicadoService;
    }

    public function index()
    {
        $comunicados = $this->comunicadoService->getAllWithUser();
        return response()->json($comunicados);
    }

    public function show(Comunicado $comunicado)
    {
        $comunicado->load('user:id,name'); 
        
        return response()->json($comunicado);
    }
}
