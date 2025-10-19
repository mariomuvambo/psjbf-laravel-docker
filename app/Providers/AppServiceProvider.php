<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // ✅ importa o URL aqui dentro do namespace

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ força HTTPS apenas em produção
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
