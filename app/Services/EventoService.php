<?php

namespace App\Services;

use App\Models\Evento;
use App\Repositories\Contracts\EventoRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EventoService
{
    protected EventoRepositoryInterface $eventoRepository;

    public function __construct(EventoRepositoryInterface $eventoRepository)
    {
        $this->eventoRepository = $eventoRepository;
    }

    public function getPaginatedAsc(int $perPage = 10): LengthAwarePaginator
    {
        return $this->eventoRepository->getPaginatedAsc($perPage);
    }

    public function getAllWithUserAsc(): Collection
    {
        return $this->eventoRepository->getAllWithUserAsc();
    }

    public function getUpcomingPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return $this->eventoRepository->getUpcomingPaginated($perPage);
    }

    public function countUpcoming(): int
    {
        return $this->eventoRepository->countUpcoming();
    }

    public function create(array $data, int $userId): Evento
    {
        $data['user_id'] = $userId;
        return $this->eventoRepository->create($data);
    }

    public function update(Evento $evento, array $data): bool
    {
        return $this->eventoRepository->update($evento, $data);
    }

    public function delete(Evento $evento): bool
    {
        return $this->eventoRepository->delete($evento);
    }
}
