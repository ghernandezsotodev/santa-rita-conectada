<?php
namespace App\Http\Controllers\Portal;
use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Services\EventoService;

class EventoController extends Controller
{
    protected EventoService $eventoService;
    public function __construct(EventoService $eventoService) { $this->eventoService = $eventoService; }

    public function index() {
        $eventos = $this->eventoService->getUpcomingPaginated(10);
        return view('portal.eventos.index', compact('eventos'));
    }
    public function show(Evento $evento) { return view('portal.eventos.show', compact('evento')); }
}
