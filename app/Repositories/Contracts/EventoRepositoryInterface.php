<?php
namespace App\Repositories\Contracts;
use App\Models\Evento;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EventoRepositoryInterface
{
    public function getPaginatedAsc(int $perPage = 10): LengthAwarePaginator;
    public function getAllWithUserAsc(): Collection;
    public function getUpcomingPaginated(int $perPage = 10): LengthAwarePaginator;
    public function countUpcoming(): int;
    public function create(array $data): Evento;
    public function update(Evento $evento, array $data): bool;
    public function delete(Evento $evento): bool;
}
