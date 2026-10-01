<?php

namespace App\Http\Controllers\Portal;

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
        $comunicados = $this->comunicadoService->getEnviadosPaginated(10);
        return view('portal.comunicados.index', compact('comunicados'));
    }

    public function show(Comunicado $comunicado)
    {
        if (!$comunicado->fecha_envio) {
            abort(404);
        }

        return view('portal.comunicados.show', compact('comunicado'));
    }
}
