<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comunicado;
use App\Services\ComunicadoService;
use App\Http\Requests\ComunicadoRequest; 

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

    public function store(ComunicadoRequest $request)
    {
        $data = $request->validated();
        
        $data['user_id'] = auth()->id(); 

        $comunicado = $this->comunicadoService->create($data);

        $this->comunicadoService->enviar($comunicado);

        return response()->json($comunicado, 201);
    }
}