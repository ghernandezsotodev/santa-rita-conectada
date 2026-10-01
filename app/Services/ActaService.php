<?php

namespace App\Services;

use App\Models\Acta;
use App\Repositories\Contracts\ActaRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\UploadedFile;

class ActaService
{
    protected ActaRepositoryInterface $actaRepository;

    public function __construct(ActaRepositoryInterface $actaRepository)
    {
        $this->actaRepository = $actaRepository;
    }

    public function getAllDesc(): Collection
    {
        return $this->actaRepository->getAllDesc();
    }

    public function getPaginatedDesc(int $perPage = 10): LengthAwarePaginator
    {
        return $this->actaRepository->getPaginatedDesc($perPage);
    }

    public function getAllWithSignedUrls(): Collection
    {
        $actas = $this->actaRepository->getAllDesc();
        return $actas->map(fn($a) => $this->injectSignedUrl($a));
    }

    protected function injectSignedUrl(Acta $acta): Acta
    {
        if ($acta->archivo_path) {
            $acta->archivo_path = URL::temporarySignedRoute(
                'actas.publico',
                now()->addMinutes(180),
                ['acta' => $acta->id]
            );
        }
        return $acta;
    }

    public function createWithFile(array $data, UploadedFile $file, int $userId): Acta
    {
        $data['user_id'] = $userId;
        $data['archivo_path'] = $file->store('actas', 'public');

        return $this->actaRepository->create($data);
    }

    public function updateWithFile(Acta $acta, array $data, ?UploadedFile $file): bool
    {
        if ($file) {
            if ($acta->archivo_path && Storage::disk('public')->exists($acta->archivo_path)) {
                Storage::disk('public')->delete($acta->archivo_path);
            }
            $data['archivo_path'] = $file->store('actas', 'public');
        }

        return $this->actaRepository->update($acta, $data);
    }

    public function deleteWithFile(Acta $acta): bool
    {
        if ($acta->archivo_path && Storage::disk('public')->exists($acta->archivo_path)) {
            Storage::disk('public')->delete($acta->archivo_path);
        }
        return $this->actaRepository->delete($acta);
    }
}
