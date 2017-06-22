<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('about/privacy', function () {
    return view('privacy');
});

Route::get('/', function () {
    return view('pages.welcome')->with(['pageTitle' => 'Welcome to Selfy']);
});

Route::get('/facebook/link', 'App\UserController@confirm_facebook_link');

Route::group(['prefix' => 'admin', 'namespace' => 'Admin' , 'middleware' => 'auth'], function (){
    Route::get('/dashboard', [
        'as' => 'SelfyDashboard',
        'uses' => 'AdminController@index'
    ]);

    Route::get('roles', 'RolesController@index');
    Route::post('roles', 'RolesController@createRole');
    Route::post('roles/edit/{id}', 'RolesController@editRole');
    Route::post('roles/removeRole/{id}', 'RolesController@removeRole');
    Route::post('roles/rolePermissions/{id}', 'RolesController@rolePermissions');

    Route::get('permissions', 'PermissionsController@index');
    Route::post('permissions', 'PermissionsController@createPermission');
    Route::post('permissions/edit/{id}', 'PermissionsController@editPermission');
    Route::post('permissions/removePermission/{id}', 'PermissionsController@removePermission');
});


Route::group(['prefix' => 'admin', 'namespace' => 'Admin' , 'middleware' => 'web'], function (){
    Route::get('/login', [
        'as' => 'login',
        'uses' => 'LoginAdminController@formLogin'
    ]);
    Route::post('/login', [
        'as' => 'login',
        'uses' => 'LoginAdminController@login'
    ]);

});

/**
 * Solo Admin y Moderadores
 * Example
 * 'middleware' => 'App\Http\Middleware\AclMiddleware:list-users'
 * return Unauthorized action. si el usuario no tiene dichos permisos
 */
Route::group(['prefix' => 'admin', 'namespace' => 'Admin' , 'middleware' => 'App\Http\Middleware\AclMiddleware:roles-crud'], function () {
    Route::get('/dashboard', [
        'as' => 'login',
        'uses' => 'AdminController@index'
    ]);
});