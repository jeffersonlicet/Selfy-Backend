<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Validator;

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
