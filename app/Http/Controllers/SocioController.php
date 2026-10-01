<?php

namespace App\Http\Controllers;

use App\Models\Socio;
use App\Http\Requests\SocioRequest;
use App\Services\SocioService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class SocioController extends Controller
{
    protected SocioService $socioService;

    public function __construct(SocioService $socioService)
    {
        $this->socioService = $socioService;
    }

    public function index(Request $request)
    {
        $socios = $this->socioService->getPaginatedWithSearch($request->input('search'), 10)->withQueryString();
        return view('socios.index', compact('socios'));
    }

    public function create()
    {
        $estadosCiviles = ['Soltero/a', 'Casado/a', 'Viudo/a', 'Divorciado/a', 'Conviviente Civil'];
        $profesiones = ['Dueña de Casa', 'Estudiante', 'Jubilado/a', 'Obrero/a', 'Técnico/a', 'Profesional', 'Agricultor/a', 'Otro'];
        return view('socios.create', compact('estadosCiviles', 'profesiones'));
    }

    public function store(SocioRequest $request)
    {
        // 1. Delegamos la transacción al servicio
        $result = $this->socioService->createWithUser($request->validated());

        // 2. Preparamos la respuesta visual
        $successMessage = '¡Socio agregado exitosamente!';

        if ($result['temporaryPassword']) {
            session()->flash('password_info', $result['temporaryPassword']);
            $successMessage = '¡Socio y usuario creados! Entregue la contraseña temporal al socio de forma segura.';
        }

        return redirect()->route('socios.index')->with('success', $successMessage);
    }

    public function show(Socio $socio)
    {
        return view('socios.show', compact('socio'));
    }

    public function edit(Socio $socio)
    {
        $estadosCiviles = ['Soltero/a', 'Casado/a', 'Viudo/a', 'Divorciado/a', 'Conviviente Civil'];
        $profesiones = ['Dueña de Casa', 'Estudiante', 'Jubilado/a', 'Obrero/a', 'Técnico/a', 'Profesional', 'Agricultor/a', 'Otro'];
        return view('socios.edit', compact('socio', 'estadosCiviles', 'profesiones'));
    }

    public function update(SocioRequest $request, Socio $socio)
    {
        $this->socioService->update($socio, $request->validated());
        return redirect()->route('socios.index')->with('success', '¡Socio actualizado exitosamente!');
    }

    public function destroy(Socio $socio)
    {
        try {
            $this->socioService->deleteWithUser($socio);
            return redirect()->route('socios.index')->with('success', 'Socio y su cuenta de acceso eliminados exitosamente.');
        } catch (QueryException $e) {
            // Mantenemos la captura de excepción de BD en el controlador para manejar el redirect adecuado
            if ($e->getCode() === '23000') {
                return redirect()->back()->with('error', 'No se puede eliminar este socio porque tiene registros asociados (como subsidios o transacciones). Por favor, elimine primero esos registros.');
            }
            return redirect()->back()->with('error', 'Ocurrió un error en la base de datos al intentar eliminar al socio.');
        }
    }
}
