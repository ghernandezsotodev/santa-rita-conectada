<?php
namespace App\Http\Controllers;
use App\Models\Evento;
use App\Http\Requests\EventoRequest;
use App\Services\EventoService;

class EventoController extends Controller
{
    protected EventoService $eventoService;
    public function __construct(EventoService $eventoService) { $this->eventoService = $eventoService; }

    public function index() {
        $eventos = $this->eventoService->getPaginatedAsc(10);
        return view('eventos.index', compact('eventos'));
    }
    public function create() { return view('eventos.create'); }
    
    public function store(EventoRequest $request) {
        $this->eventoService->create($request->validated(), auth()->id());
        return redirect()->route('eventos.index')->with('success', '¡Evento creado exitosamente!');
    }
    
    public function edit(Evento $evento) { return view('eventos.edit', compact('evento')); }
    
    public function update(EventoRequest $request, Evento $evento) {
        $this->eventoService->update($evento, $request->validated());
        return redirect()->route('eventos.index')->with('success', '¡Evento actualizado exitosamente!');
    }
    
    public function destroy(Evento $evento) {
        $this->eventoService->delete($evento);
        return redirect()->route('eventos.index')->with('success', '¡Evento eliminado exitosamente!');
    }
    public function show(Evento $evento){}
}
