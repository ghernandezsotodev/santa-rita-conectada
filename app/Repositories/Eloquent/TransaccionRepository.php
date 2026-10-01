<?php

namespace App\Repositories\Eloquent;

use App\Models\Transaccion;
use App\Repositories\Contracts\TransaccionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TransaccionRepository implements TransaccionRepositoryInterface
{
    public function getPaginatedWithRelations(int $perPage = 15): LengthAwarePaginator
    {
        return Transaccion::with('user', 'socio')->orderBy('fecha', 'desc')->paginate($perPage);
    }

    public function getAllLatest(): Collection
    {
        return Transaccion::latest('fecha')->get();
    }

    public function getPaginatedBySocio(int $socioId, int $perPage = 10): LengthAwarePaginator
    {
        return Transaccion::where('socio_id', $socioId)->latest('fecha')->paginate($perPage);
    }

    public function getSumByType(string $tipo): float
    {
        return (float) Transaccion::where('tipo', $tipo)->sum('monto');
    }

    public function getSumByTypeForSocio(int $socioId, string $tipo): float
    {
        return (float) Transaccion::where('socio_id', $socioId)->where('tipo', $tipo)->sum('monto');
    }

    public function getSumByTypeAndMonthYear(string $tipo, int $month, int $year): float
    {
        return (float) Transaccion::where('tipo', $tipo)
                                  ->whereYear('fecha', $year)
                                  ->whereMonth('fecha', $month)
                                  ->sum('monto');
    }

    public function getSumByTypeAndMonthYearForSocio(int $socioId, string $tipo, int $month, int $year): float
    {
        return (float) Transaccion::where('socio_id', $socioId)
                                  ->where('tipo', $tipo)
                                  ->whereYear('fecha', $year)
                                  ->whereMonth('fecha', $month)
                                  ->sum('monto');
    }

    public function create(array $data): Transaccion
    {
        return Transaccion::create($data);
    }

    public function update(Transaccion $transaccion, array $data): bool
    {
        return $transaccion->update($data);
    }

    public function delete(Transaccion $transaccion): bool
    {
        return $transaccion->delete();
    }
}
