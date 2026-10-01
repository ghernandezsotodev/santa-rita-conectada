<?php
namespace App\Repositories\Eloquent;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findBySocioId(int $socioId): ?User
    {
        return User::where('socio_id', $socioId)->first();
    }

    public function getUsersWithRolesAndFcmToken(): Collection
    {
        return User::whereHas('roles')->whereNotNull('fcm_token')->get();
    }

    public function createWithRole(array $data, string $roleName): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $user = User::create($data);
        $user->assignRole($roleName);
        
        return $user;
    }

    public function updateFcmToken(User $user, string $token): bool
    {
        return $user->update(['fcm_token' => $token]);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
