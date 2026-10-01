<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\ComunicadoRepositoryInterface;
use App\Repositories\Eloquent\ComunicadoRepository;
use App\Repositories\Contracts\SocioRepositoryInterface;
use App\Repositories\Eloquent\SocioRepository;
use App\Repositories\Contracts\TransaccionRepositoryInterface;
use App\Repositories\Eloquent\TransaccionRepository;
use App\Repositories\Contracts\ActaRepositoryInterface;
use App\Repositories\Eloquent\ActaRepository;
use App\Repositories\Contracts\EventoRepositoryInterface;
use App\Repositories\Eloquent\EventoRepository;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ComunicadoRepositoryInterface::class, ComunicadoRepository::class);
        $this->app->bind(SocioRepositoryInterface::class, SocioRepository::class);
        $this->app->bind(TransaccionRepositoryInterface::class, TransaccionRepository::class);
        $this->app->bind(ActaRepositoryInterface::class, ActaRepository::class);
        $this->app->bind(EventoRepositoryInterface::class, EventoRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
