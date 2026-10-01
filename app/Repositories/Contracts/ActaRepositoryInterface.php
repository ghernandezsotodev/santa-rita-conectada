<?php
namespace App\Repositories\Contracts;
use App\Models\Acta;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ActaRepositoryInterface
{
    public function getAllDesc(): Collection;
    public function getPaginatedDesc(int $perPage = 10): LengthAwarePaginator;
    public function create(array $data): Acta;
    public function update(Acta $acta, array $data): bool;
    public function delete(Acta $acta): bool;
}
