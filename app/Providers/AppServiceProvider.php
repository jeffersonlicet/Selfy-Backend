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
            'play' => 'App\Models\ChallengePlay',
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        $this->app->register(\Krlove\EloquentModelGenerator\Provider\GeneratorServiceProvider::class);
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
    }
}
