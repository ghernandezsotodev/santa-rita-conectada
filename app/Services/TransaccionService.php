<?php

namespace App\Services;

use App\Models\Transaccion;
use App\Repositories\Contracts\TransaccionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class TransaccionService
{
    protected TransaccionRepositoryInterface $transaccionRepository;

    public function __construct(TransaccionRepositoryInterface $transaccionRepository)
    {
        $this->transaccionRepository = $transaccionRepository;
    }

    // --- MÉTODOS DE LECTURA Y BALANCES ---

    public function getPaginatedWithRelations(int $perPage = 15): LengthAwarePaginator
    {
        return $this->transaccionRepository->getPaginatedWithRelations($perPage);
    }

    public function getGlobalBalance(): array
    {
        $ingresos = $this->transaccionRepository->getSumByType('Ingreso');
        $egresos = $this->transaccionRepository->getSumByType('Egreso');
        return [
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'balance' => $ingresos - $egresos
        ];
    }

    public function getPersonalBalance(int $socioId): array
    {
        $ingresos = $this->transaccionRepository->getSumByTypeForSocio($socioId, 'Ingreso');
        $egresos = $this->transaccionRepository->getSumByTypeForSocio($socioId, 'Egreso');
        return [
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'balance' => $ingresos - $egresos
        ];
    }

    // --- MÉTODOS CON URL FIRMADA PARA API ---

    public function getAllLatestWithSignedUrls(): Collection
    {
        $transacciones = $this->transaccionRepository->getAllLatest();
        return $transacciones->map(fn($t) => $this->injectSignedUrl($t));
    }

    public function getPaginatedBySocioWithSignedUrls(int $socioId, int $perPage = 10): LengthAwarePaginator
    {
        $transacciones = $this->transaccionRepository->getPaginatedBySocio($socioId, $perPage);
        $transacciones->getCollection()->transform(fn($t) => $this->injectSignedUrl($t));
        return $transacciones;
    }

    protected function injectSignedUrl(Transaccion $transaccion): Transaccion
    {
        if ($transaccion->comprobante_path) {
            $transaccion->comprobante_path = URL::temporarySignedRoute(
                'comprobantes.publico', 
                now()->addMinutes(30),
                ['transaccion' => $transaccion->id]
            );
        }
        return $transaccion;
    }

    // --- MÉTODOS DE ESCRITURA (ARCHIVOS Y BD) ---

    public function createWithComprobante(array $data, ?UploadedFile $file, int $userId): Transaccion
    {
        $data['user_id'] = $userId;

        if ($file) {
            $data['comprobante_path'] = $file->store('comprobantes', 'public');
        }

        return $this->transaccionRepository->create($data);
    }

    public function updateWithComprobante(Transaccion $transaccion, array $data, ?UploadedFile $file): bool
    {
        if ($file) {
            if ($transaccion->comprobante_path) {
                Storage::disk('public')->delete($transaccion->comprobante_path);
            }
            $data['comprobante_path'] = $file->store('comprobantes', 'public');
        }

        return $this->transaccionRepository->update($transaccion, $data);
    }

    public function deleteWithComprobante(Transaccion $transaccion): bool
    {
        if ($transaccion->comprobante_path) {
            Storage::disk('public')->delete($transaccion->comprobante_path);
        }
        return $this->transaccionRepository->delete($transaccion);
    }

    // --- MÉTODOS PARA GRÁFICOS (CHARTS) ---

    public function getGlobalChartData(): array
    {
        $labels = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i)->locale('es');
            $year = $date->format('Y');
            $month = $date->month;

            $labels[] = ucfirst($date->translatedFormat('F'));
            $incomeData[] = $this->transaccionRepository->getSumByTypeAndMonthYear('Ingreso', $month, $year);
            $expenseData[] = $this->transaccionRepository->getSumByTypeAndMonthYear('Egreso', $month, $year);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Ingresos', 'data' => $incomeData, 'backgroundColor' => '#4ade80'],
                ['label' => 'Egresos', 'data' => $expenseData, 'backgroundColor' => '#f87171']
            ]
        ];
    }

    public function getPersonalChartData(int $socioId): array
    {
        $labels = [];
        $contributionData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i)->locale('es');
            $year = $date->format('Y');
            $month = $date->month;

            $labels[] = ucfirst($date->translatedFormat('F'));
            $contributionData[] = $this->transaccionRepository->getSumByTypeAndMonthYearForSocio($socioId, 'Ingreso', $month, $year);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Mis Aportes',
                    'data' => $contributionData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'fill' => true,
                    'tension' => 0.1
                ]
            ]
        ];
    }
}
