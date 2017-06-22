<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 6/5/2017
 * Time: 10:21 PM
 */

return [
    /*
    |--------------------------------------------------------------------------
    | App Name
    |--------------------------------------------------------------------------
    |
    | Name of your app, will be displayed in the top left corner
    |
    */

    'appName' => "Selfy Admin Panel",

    /*
    |--------------------------------------------------------------------------
    | Route After Login
    |--------------------------------------------------------------------------
    |
    | Name of the route where you want to go after the login
    |
    */

    'afterLoginRoute' => 'dashboard',

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix for admin routes, it defaults to backend but you can change it to admin
    | This will make the routes have a prefix of admin like http://example.com/admin/
    |
    */

    'routePrefix' => 'admin',

    /*
    |--------------------------------------------------------------------------
    | Middleware Stack
    |--------------------------------------------------------------------------
    |
    | Middleware Stack to apply to the package routes, this will be applied to
    | all routes in the package
    |
    */

    'middleware' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Application Version
    |--------------------------------------------------------------------------
    |
    | Your application version, it will be displayed in the footer
    |
    */

    'version' => '1.0',

    /*
    |--------------------------------------------------------------------------
    | Application copyright
    |--------------------------------------------------------------------------
    |
    | Your application copyright, it will be displayed in the footer
    |
    */

    'copyright' => 'Copyright © Selfy Admin - All rights reserved.'
];
