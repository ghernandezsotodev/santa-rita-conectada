<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaccion;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use App\Services\TransaccionService;

class AporteController extends Controller
{
    protected TransaccionService $transaccionService;

    public function __construct(TransaccionService $transaccionService)
    {
        $this->transaccionService = $transaccionService;
    }

    public function index(): View
    {
        $socio = Auth::user()->socio;
        $datosAportes = ['balancePersonal' => 0, 'transacciones' => collect()];

        if ($socio) {
            $balances = $this->transaccionService->getPersonalBalance($socio->id);
            $datosAportes['balancePersonal'] = $balances['balance'];
            
            // Reutilizamos el método de URLs firmadas para mantener consistencia, 
            // aunque en web clásica no siempre se usen.
            $datosAportes['transacciones'] = $this->transaccionService->getPaginatedBySocioWithSignedUrls($socio->id, 10);
        }

        return view('portal.aportes.index', $datosAportes);
    }

    public function descargarComprobante(Transaccion $transaccion)
    {
        if ($transaccion->socio_id !== Auth::user()->socio->id) {
            abort(403, 'No tienes permiso para ver este comprobante.');
        }
        if (!$transaccion->comprobante_path || !Storage::disk('public')->exists($transaccion->comprobante_path)) {
            return back()->with('error', 'Esta transacción no tiene un comprobante adjunto.');
        }
        return Storage::disk('public')->download($transaccion->comprobante_path);
    }

    public function descargarPublico(Transaccion $transaccion)
    {
        if (!$transaccion->comprobante_path || !Storage::disk('public')->exists($transaccion->comprobante_path)) {
            abort(404, 'El archivo ya no existe o fue movido.');
        }
        return Storage::disk('public')->download($transaccion->comprobante_path);
    }
}
