<?php
namespace App\Repositories\Contracts;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;
    public function findBySocioId(int $socioId): ?User;
    public function getUsersWithRolesAndFcmToken(): Collection;
    public function createWithRole(array $data, string $roleName): User;
    public function updateFcmToken(User $user, string $token): bool;
    public function delete(User $user): bool;
}
