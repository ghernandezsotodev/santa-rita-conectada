<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TransaccionService;

class TransaccionController extends Controller
{
    protected TransaccionService $transaccionService;

    public function __construct(TransaccionService $transaccionService)
    {
        $this->transaccionService = $transaccionService;
    }

    public function index()
    {
        return response()->json($this->transaccionService->getAllLatestWithSignedUrls());
    }

    public function financesChart()
    {
        return response()->json($this->transaccionService->getGlobalChartData());
    }
}
