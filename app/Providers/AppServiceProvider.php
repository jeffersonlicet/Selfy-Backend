<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'duo' => 'App\Models\User',
            'spot' => 'App\Models\Place',
            'play' => 'App\Models\Play',
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register dev services
        if ($this->app->environment() !== 'production') {

            // IDE helper
            /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
            $this->app->register(\Krlove\EloquentModelGenerator\Provider\GeneratorServiceProvider::class);
            /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
            $this->app->register(\Krlove\EloquentModelGenerator\Provider\GeneratorServiceProvider::class);
        }
    }
}
