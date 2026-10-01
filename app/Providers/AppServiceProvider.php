<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\ComunicadoRepositoryInterface;
use App\Repositories\Eloquent\ComunicadoRepository;
use App\Repositories\Contracts\SocioRepositoryInterface;
use App\Repositories\Eloquent\SocioRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Enlaces de inyección de dependencias
        $this->app->bind(ComunicadoRepositoryInterface::class, ComunicadoRepository::class);
        $this->app->bind(SocioRepositoryInterface::class, SocioRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
