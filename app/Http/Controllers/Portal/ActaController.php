<?php
namespace App\Http\Controllers\Portal;
use App\Http\Controllers\Controller;
use App\Models\Acta;
use App\Services\ActaService;

class ActaController extends Controller
{
    protected ActaService $actaService;
    public function __construct(ActaService $actaService) { $this->actaService = $actaService; }

    public function index() {
        $actas = $this->actaService->getPaginatedDesc(10);
        return view('portal.actas.index', compact('actas'));
    }
    public function show(Acta $acta) { return view('portal.actas.show', compact('acta')); }
}
