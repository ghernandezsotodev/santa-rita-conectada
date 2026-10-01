<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SocioService;

class SocioController extends Controller
{
    protected SocioService $socioService;

    public function __construct(SocioService $socioService)
    {
        $this->socioService = $socioService;
    }

    public function index()
    {
        return response()->json($this->socioService->getAllOrderedByName());
    }
}
