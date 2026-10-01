<?php

namespace App\Repositories\Contracts;

use App\Models\Comunicado;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ComunicadoRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;
    public function getEnviadosPaginated(int $perPage = 10): LengthAwarePaginator;
    public function getAllWithUser(): Collection;
    public function create(array $data): Comunicado;
    public function update(Comunicado $comunicado, array $data): bool;
    public function delete(Comunicado $comunicado): bool;
}
