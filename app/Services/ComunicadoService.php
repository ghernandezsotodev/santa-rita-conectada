<?php

namespace App\Services;

use App\Models\Comunicado;
use App\Models\Socio;
use App\Models\User; 
use App\Notifications\NuevoComunicadoNotification;
use App\Notifications\PushComunicadoNotification; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Repositories\Contracts\ComunicadoRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ComunicadoService
{
    protected ComunicadoRepositoryInterface $comunicadoRepository;

    public function __construct(ComunicadoRepositoryInterface $comunicadoRepository)
    {
        $this->comunicadoRepository = $comunicadoRepository;
    }

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->comunicadoRepository->getAllPaginated($perPage);
    }

    public function getEnviadosPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return $this->comunicadoRepository->getEnviadosPaginated($perPage);
    }

    public function getAllWithUser(): Collection
    {
        return $this->comunicadoRepository->getAllWithUser();
    }

    public function create(array $data): Comunicado
    {
        return $this->comunicadoRepository->create($data);
    }

    public function update(Comunicado $comunicado, array $data): bool
    {
        return $this->comunicadoRepository->update($comunicado, $data);
    }

    public function delete(Comunicado $comunicado): bool
    {
        return $this->comunicadoRepository->delete($comunicado);
    }

    public function enviar(Comunicado $comunicado): void
    {
        if (is_null($comunicado->fecha_envio)) {
            $this->comunicadoRepository->update($comunicado, ['fecha_envio' => now()]);
        }

        $sociosParaEmail = Socio::whereRaw("LOWER(estado) = 'activo'")
                                ->whereNotNull('email')
                                ->get();

        if ($sociosParaEmail->isNotEmpty()) {
            Notification::send($sociosParaEmail, new NuevoComunicadoNotification($comunicado));
            Log::info('[ComunicadoService] Encolando Emails (vía Socio) para ' . $sociosParaEmail->count() . ' socios.');
        } else {
            Log::info('[ComunicadoService] No se encontraron socios activos con email para notificar.');
        }

        $usuariosParaPush = User::whereHas('roles')
                                ->whereNotNull('fcm_token')
                                ->get();

        if ($usuariosParaPush->isNotEmpty()) {
            Notification::send($usuariosParaPush, new PushComunicadoNotification($comunicado));
            Log::info('[ComunicadoService] Encolando Notificaciones Push (vía User) para ' . $usuariosParaPush->count() . ' usuarios.');
        } else {
            Log::info('[ComunicadoService] No se encontraron usuarios (rol Socio) con fcm_token para notificar.');
        }
    }
}
