<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\ComunicadoRepositoryInterface;
use App\Repositories\Eloquent\ComunicadoRepository;
use App\Repositories\Contracts\SocioRepositoryInterface;
use App\Repositories\Eloquent\SocioRepository;
use App\Repositories\Contracts\TransaccionRepositoryInterface;
use App\Repositories\Eloquent\TransaccionRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ComunicadoRepositoryInterface::class, ComunicadoRepository::class);
        $this->app->bind(SocioRepositoryInterface::class, SocioRepository::class);
        $this->app->bind(TransaccionRepositoryInterface::class, TransaccionRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
