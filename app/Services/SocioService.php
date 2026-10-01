<?php
namespace App\Services;
use App\Models\Socio;
use App\Repositories\Contracts\SocioRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SocioService
{
    protected SocioRepositoryInterface $socioRepository;
    protected UserService $userService; // INYECTAMOS USER SERVICE

    public function __construct(SocioRepositoryInterface $socioRepository, UserService $userService)
    {
        $this->socioRepository = $socioRepository;
        $this->userService = $userService;
    }

    public function getPaginatedWithSearch(?string $searchTerm, int $perPage = 10): LengthAwarePaginator {
        return $this->socioRepository->getPaginatedWithSearch($searchTerm, $perPage);
    }
    public function getAllOrderedByName(): Collection {
        return $this->socioRepository->getAllOrderedByName();
    }
    public function countAll(): int {
        return $this->socioRepository->countAll();
    }

    public function createWithUser(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $socio = $this->socioRepository->create($data);
            $temporaryPassword = null;

            if (!empty($data['email'])) {
                $temporaryPassword = Str::random(10);
                
                $this->userService->createSocioUser([
                    'name' => $data['nombre'],
                    'email' => $data['email'],
                    'password' => $temporaryPassword,
                    'socio_id' => $socio->id,
                ]);
            }

            return ['socio' => $socio, 'temporaryPassword' => $temporaryPassword];
        });
    }

    public function update(Socio $socio, array $data): bool {
        return $this->socioRepository->update($socio, $data);
    }

    public function deleteWithUser(Socio $socio): void
    {
        DB::transaction(function () use ($socio) {
            $this->userService->deleteBySocioId($socio->id);
            $this->socioRepository->delete($socio);
        });
    }
}
