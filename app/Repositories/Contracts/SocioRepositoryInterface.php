<?php

namespace App\Repositories\Contracts;

use App\Models\Socio;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SocioRepositoryInterface
{
    public function getPaginatedWithSearch(?string $searchTerm, int $perPage = 10): LengthAwarePaginator;
    public function getAllOrderedByName(): Collection;
    public function getActiveWithEmail(): Collection;
    public function countAll(): int;
    public function create(array $data): Socio;
    public function update(Socio $socio, array $data): bool;
    public function delete(Socio $socio): bool;
}
