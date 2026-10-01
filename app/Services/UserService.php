<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Autentica a un usuario para la API y le genera un token de Sanctum.
     */
    public function authenticateApi(string $email, string $password, string $deviceName): ?array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            return null; // Credenciales inválidas
        }

        $token = $user->createToken($deviceName)->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * Revoca el token actual del usuario en la API.
     */
    public function logoutApi(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Actualiza el token de notificaciones Push (FCM).
     */
    public function updateFcmToken(User $user, string $token): bool
    {
        return $this->userRepository->updateFcmToken($user, $token);
    }

    /**
     * Obtiene usuarios con tokens válidos para notificaciones (usado por ComunicadoService).
     */
    public function getUsersForPushNotifications(): Collection
    {
        return $this->userRepository->getUsersWithRolesAndFcmToken();
    }

    /**
     * Crea un usuario asociado a un Socio (usado por SocioService).
     */
    public function createSocioUser(array $data): User
    {
        return $this->userRepository->createWithRole($data, 'Socio');
    }

    /**
     * Elimina el usuario asociado a un socio (usado por SocioService).
     */
    public function deleteBySocioId(int $socioId): void
    {
        $user = $this->userRepository->findBySocioId($socioId);
        if ($user) {
            $this->userRepository->delete($user);
        }
    }
}
