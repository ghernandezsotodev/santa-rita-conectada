<?php

namespace App\Http\Controllers;

use App\Models\Socio;
use App\Models\Transaccion;
use App\Http\Requests\TransaccionRequest;
use App\Services\TransaccionService;
use Illuminate\Http\Request;

class TransaccionController extends Controller
{
    protected TransaccionService $transaccionService;

    public function __construct(TransaccionService $transaccionService)
    {
        $this->transaccionService = $transaccionService;
    }

    public function index()
    {
        $transacciones = $this->transaccionService->getPaginatedWithRelations(15);
        $balances = $this->transaccionService->getGlobalBalance();
        
        return view('transacciones.index', array_merge(compact('transacciones'), $balances));
    }

    public function create(Request $request)
    {
        $tipo = $request->query('tipo');
        if (!in_array($tipo, ['Ingreso', 'Egreso'])) {
            return redirect()->route('transacciones.index')->with('error', 'Tipo de transacción no válido.');
        }
        $socios = Socio::orderBy('nombre')->get();
        return view('transacciones.create', compact('tipo', 'socios'));
    }

    public function store(TransaccionRequest $request)
    {
        $this->transaccionService->createWithComprobante(
            $request->validated(), 
            $request->file('comprobante'), 
            auth()->id()
        );

        return redirect()->route('transacciones.index')->with('success', '¡Transacción registrada exitosamente!');
    }

    public function show(Transaccion $transaccion)
    {
        return view('transacciones.show', compact('transaccion'));
    }

    public function edit(Transaccion $transaccion)
    {
        $tipo = $transaccion->tipo;
        $socios = Socio::orderBy('nombre')->get();
        return view('transacciones.edit', compact('transaccion', 'tipo', 'socios'));
    }

    public function update(TransaccionRequest $request, Transaccion $transaccion)
    {
        $this->transaccionService->updateWithComprobante(
            $transaccion, 
            $request->validated(), 
            $request->file('comprobante')
        );

        return redirect()->route('transacciones.index')->with('success', '¡Transacción actualizada exitosamente!');
    }

    public function destroy(Transaccion $transaccion)
    {
        $this->transaccionService->deleteWithComprobante($transaccion);
        return redirect()->route('transacciones.index')->with('success', 'Transacción eliminada exitosamente.');
    }
}
