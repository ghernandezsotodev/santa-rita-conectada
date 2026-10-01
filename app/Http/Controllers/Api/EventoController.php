<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Services\EventoService;

class EventoController extends Controller
{
    protected EventoService $eventoService;
    public function __construct(EventoService $eventoService) { $this->eventoService = $eventoService; }

    public function index() { return response()->json($this->eventoService->getAllWithUserAsc()); }
    
    public function show(Evento $evento) {
        $evento->load('user:id,name');
        return response()->json($evento);
    }
}
