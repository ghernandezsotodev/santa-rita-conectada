<?php

namespace App\Repositories\Eloquent;

use App\Models\Comunicado;
use App\Repositories\Contracts\ComunicadoRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ComunicadoRepository implements ComunicadoRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Comunicado::with("user")->orderBy("created_at", "desc")->paginate($perPage);
    }

    public function getEnviadosPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Comunicado::whereNotNull("fecha_envio")->latest("fecha_envio")->paginate($perPage);
    }

    public function getAllWithUser(): Collection
    {
        return Comunicado::with("user:id,name")->orderBy("created_at", "desc")->get();
    }

    public function create(array $data): Comunicado
    {
        return Comunicado::create($data);
    }

    public function update(Comunicado $comunicado, array $data): bool
    {
        return $comunicado->update($data);
    }

    public function delete(Comunicado $comunicado): bool
    {
        return $comunicado->delete();
    }
}
