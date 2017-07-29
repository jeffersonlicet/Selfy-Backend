<?php

namespace App\Providers;

use Validator,
    Illuminate\Support\ServiceProvider,
    Illuminate\Database\Eloquent\Relations\Relation;

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

        Validator::extend(
            'allowed_username',
            'App\Validation\AllowedUsernameValidator@validate'
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
            $this->app->register(\Krlove\EloquentModelGenerator\Provider\GeneratorServiceProvider::class);
            /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
