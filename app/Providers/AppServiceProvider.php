<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Contracts\ComunicadoRepositoryInterface;
use App\Repositories\Eloquent\ComunicadoRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ComunicadoRepositoryInterface::class, 
            ComunicadoRepository::class
        );
    }
    public function boot(): void
    {
        
    }
}