<?php

namespace App\Repositories\Eloquent;

use App\Models\Socio;
use App\Repositories\Contracts\SocioRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Freshwork\ChileanBundle\Rut;
use InvalidArgumentException;

class SocioRepository implements SocioRepositoryInterface
{
    public function getPaginatedWithSearch(?string $searchTerm, int $perPage = 10): LengthAwarePaginator
    {
        $query = Socio::query();

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nombre', 'like', '%' . $searchTerm . '%');
                try {
                    $normalizedRut = Rut::parse($searchTerm)->normalize();
                    $q->orWhere('rut', '=', $normalizedRut);
                } catch (InvalidArgumentException $e) {
                    // Ignorar excepción de formato, buscar solo por nombre
                }
            });
        }

        return $query->orderBy('nombre')->paginate($perPage);
    }

    public function getAllOrderedByName(): Collection
    {
        return Socio::orderBy('nombre')->get();
    }

    public function getActiveWithEmail(): Collection
    {
        return Socio::whereRaw("LOWER(estado) = 'activo'")
                    ->whereNotNull('email')
                    ->get();
    }

    public function countAll(): int
    {
        return Socio::count();
    }

    public function create(array $data): Socio
    {
        return Socio::create($data);
    }

    public function update(Socio $socio, array $data): bool
    {
        return $socio->update($data);
    }

    public function delete(Socio $socio): bool
    {
        return $socio->delete();
    }
}
