<?php

namespace App\Http\Controllers;

use App\Models\Comunicado;
use App\Http\Requests\ComunicadoRequest;
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
        $comunicados = $this->comunicadoService->getAllPaginated(15);
        return view('comunicados.index', compact('comunicados'));
    }

    public function create()
    {
        return view('comunicados.create');
    }

    public function store(ComunicadoRequest $request)
    {
        // Obtenemos los datos validados y agregamos el usuario autenticado
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $this->comunicadoService->create($data);

        return redirect()->route('comunicados.index')
                         ->with('success', '¡Comunicado creado exitosamente!');
    }

    public function show(Comunicado $comunicado)
    {
        return view('comunicados.show', compact('comunicado'));
    }

    public function edit(Comunicado $comunicado)
    {
        return view('comunicados.edit', compact('comunicado'));
    }

    public function update(ComunicadoRequest $request, Comunicado $comunicado)
    {
        $this->comunicadoService->update($comunicado, $request->validated());

        return redirect()->route('comunicados.index')
                         ->with('success', '¡Comunicado actualizado exitosamente!');
    }

    public function destroy(Comunicado $comunicado)
    {
        $this->comunicadoService->delete($comunicado);
        return redirect()->route('comunicados.index')
                         ->with('success', 'Comunicado eliminado exitosamente.');
    }

    public function enviar(Comunicado $comunicado)
    {
        if ($comunicado->fecha_envio) {
            return redirect()->route('comunicados.index')->with('error', 'Este comunicado ya fue enviado.');
        }

        $this->comunicadoService->enviar($comunicado);

        return redirect()->route('comunicados.index')
                         ->with('success', '¡El comunicado se ha puesto en la cola para ser enviado!');
    }
}
