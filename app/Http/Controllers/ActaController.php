<?php
namespace App\Http\Controllers;
use App\Models\Acta;
use App\Http\Requests\ActaRequest;
use App\Services\ActaService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ActaController extends Controller
{
    protected ActaService $actaService;
    public function __construct(ActaService $actaService) { $this->actaService = $actaService; }

    public function index() {
        $actas = $this->actaService->getAllDesc();
        return view('actas.index', compact('actas'));
    }
    public function create() { return view('actas.create'); }
    
    public function store(ActaRequest $request) {
        $this->actaService->createWithFile($request->validated(), $request->file('archivo'), auth()->id());
        return redirect()->route('actas.index')->with('success', '¡Acta subida y registrada exitosamente!');
    }
    
    public function show(Acta $acta) {
        if (!Storage::disk('public')->exists($acta->archivo_path)) return redirect()->route('actas.index')->with('error', 'El archivo no fue encontrado.');
        return Storage::disk('public')->response($acta->archivo_path);
    }
    public function edit(Acta $acta) { return view('actas.edit', compact('acta')); }
    
    public function update(ActaRequest $request, Acta $acta) {
        $this->actaService->updateWithFile($acta, $request->validated(), $request->file('archivo'));
        return redirect()->route('actas.index')->with('success', '¡Acta actualizada exitosamente!');
    }
    
    public function destroy(Acta $acta) {
        $this->actaService->deleteWithFile($acta);
        return redirect()->route('actas.index')->with('success', 'Acta eliminada exitosamente.');
    }
    
    public function descargarParaSocio(Acta $acta) {
        if (!Storage::disk('public')->exists($acta->archivo_path)) return redirect()->route('portal.actas.index')->with('error', 'Archivo no encontrado.');
        return Storage::disk('public')->download($acta->archivo_path);
    }
    
    public function descargarPublico(Request $request, Acta $acta) {
        if (!Storage::disk('public')->exists($acta->archivo_path)) abort(404, 'Archivo no encontrado.');
        return Storage::disk('public')->download($acta->archivo_path);
    }
}
