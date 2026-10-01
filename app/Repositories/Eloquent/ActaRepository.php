<?php
namespace App\Repositories\Eloquent;
use App\Models\Acta;
use App\Repositories\Contracts\ActaRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ActaRepository implements ActaRepositoryInterface
{
    public function getAllDesc(): Collection
    {
        return Acta::orderBy('fecha', 'desc')->get();
    }

    public function getPaginatedDesc(int $perPage = 10): LengthAwarePaginator
    {
        return Acta::latest()->paginate($perPage);
    }

    public function create(array $data): Acta
    {
        return Acta::create($data);
    }

    public function update(Acta $acta, array $data): bool
    {
        return $acta->update($data);
    }

    public function delete(Acta $acta): bool
    {
        return $acta->delete();
    }
}
