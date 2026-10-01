<?php

namespace App\Services;

use App\Models\Socio;
use App\Models\User;
use App\Repositories\Contracts\SocioRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SocioService
{
    protected SocioRepositoryInterface $socioRepository;

    public function __construct(SocioRepositoryInterface $socioRepository)
    {
        $this->socioRepository = $socioRepository;
    }

    public function getPaginatedWithSearch(?string $searchTerm, int $perPage = 10): LengthAwarePaginator
    {
        return $this->socioRepository->getPaginatedWithSearch($searchTerm, $perPage);
    }

    public function getAllOrderedByName(): Collection
    {
        return $this->socioRepository->getAllOrderedByName();
    }

    public function countAll(): int
    {
        return $this->socioRepository->countAll();
    }

    /**
     * Crea un socio y su usuario asociado usando transacciones.
     * Retorna un array con el socio y la contraseña temporal (si aplica).
     */
    public function createWithUser(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $socio = $this->socioRepository->create($data);
            $temporaryPassword = null;

            if (!empty($data['email'])) {
                $temporaryPassword = Str::random(10);
                
                $user = User::create([
                    'name' => $data['nombre'],
                    'email' => $data['email'],
                    'password' => Hash::make($temporaryPassword),
                    'socio_id' => $socio->id,
                ]);

                $user->assignRole('Socio');
            }

            return [
                'socio' => $socio,
                'temporaryPassword' => $temporaryPassword
            ];
        });
    }

    public function update(Socio $socio, array $data): bool
    {
        return $this->socioRepository->update($socio, $data);
    }

    /**
     * Elimina primero el usuario vinculado y luego al socio.
     */
    public function deleteWithUser(Socio $socio): void
    {
        DB::transaction(function () use ($socio) {
            $usuarioVinculado = User::where('socio_id', $socio->id)->first();
            
            if ($usuarioVinculado) {
                $usuarioVinculado->delete();
            }

            $this->socioRepository->delete($socio);
        });
    }
}
