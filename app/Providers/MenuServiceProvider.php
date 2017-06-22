<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 6/22/2017
 * Time: 11:55 AM
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use View;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Boot the provider
     */
    public function boot()
    {
        $menu = $this->app->make('admin.menu');
        $menu->addMenu([
            'users' => [
                'link' => [
                    'link' => '#',
                    'text' => '<i class="fa fa-user"></i> ' . trans('selfy-admin.usersTitle'),
                ],
                'permissions' => ['list-users'],
                'submenus' => [
                    'list' => [
                        'link' => [
                            'link' => config('selfy-admin.routePrefix', 'admin').'/users',
                            'text' => trans('selfy-admin.usersList'),
                        ],
                        'permissions' => ['list-users'],
                    ],
                    'roles' => [
                        'link' => [
                            'link' => config('selfy-admin.routePrefix', 'admin').'/roles',
                            'text' => trans('selfy-admin.userRoles'),
                        ],
                        'permissions' => ['roles-crud'],
                    ]
                ]
            ]
        ]);
        $this->setMenuComposer($menu);
    }

    /**
     * Register Stuff in the application
     */
    public function register()
    {
        $this->app->singleton('admin.menu',
            'App\Services\MenuBuilder');
    }

    /**
     * Menu View Composer
     * @param $menu
     * @return $this
     */
    private function setMenuComposer($menu)
    {
        View::composer('*', function ($view) use ($menu) {
            $view->with('menu', $menu);
        });

        return $this;
    }
}