<?php
namespace App\Repositories\Eloquent;
use App\Models\Evento;
use App\Repositories\Contracts\EventoRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EventoRepository implements EventoRepositoryInterface
{
    public function getPaginatedAsc(int $perPage = 10): LengthAwarePaginator
    {
        return Evento::orderBy('fecha_evento', 'asc')->paginate($perPage);
    }

    public function getAllWithUserAsc(): Collection
    {
        return Evento::with('user:id,name')->orderBy('fecha_evento', 'asc')->get();
    }

    public function getUpcomingPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Evento::where('fecha_evento', '>=', now())
                     ->orderBy('fecha_evento', 'asc')
                     ->paginate($perPage);
    }

    public function countUpcoming(): int
    {
        return Evento::where('fecha_evento', '>=', now())->count();
    }

    public function create(array $data): Evento
    {
        return Evento::create($data);
    }

    public function update(Evento $evento, array $data): bool
    {
        return $evento->update($data);
    }

    public function delete(Evento $evento): bool
    {
        return $evento->delete();
    }
}
