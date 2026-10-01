<?php

namespace App\Repositories\Contracts;

use App\Models\Transaccion;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TransaccionRepositoryInterface
{
    public function getPaginatedWithRelations(int $perPage = 15): LengthAwarePaginator;
    public function getAllLatest(): Collection;
    public function getPaginatedBySocio(int $socioId, int $perPage = 10): LengthAwarePaginator;
    public function getSumByType(string $tipo): float;
    public function getSumByTypeForSocio(int $socioId, string $tipo): float;
    public function getSumByTypeAndMonthYear(string $tipo, int $month, int $year): float;
    public function getSumByTypeAndMonthYearForSocio(int $socioId, string $tipo, int $month, int $year): float;
    public function create(array $data): Transaccion;
    public function update(Transaccion $transaccion, array $data): bool;
    public function delete(Transaccion $transaccion): bool;
}
