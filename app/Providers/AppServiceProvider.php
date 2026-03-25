<?php

namespace App\Providers;

use App\Models\Vente;
use App\Models\Commande;
use App\Observers\VenteObserver;
use App\Observers\CommandeObserver;
use Illuminate\Support\ServiceProvider;


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
        Vente::observe(VenteObserver::class);
        Commande::observe(CommandeObserver::class);
    }

}
