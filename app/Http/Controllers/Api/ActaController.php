<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Services\ActaService;

class ActaController extends Controller
{
    protected ActaService $actaService;
    public function __construct(ActaService $actaService) { $this->actaService = $actaService; }

    public function index() { return response()->json($this->actaService->getAllWithSignedUrls()); }
}
