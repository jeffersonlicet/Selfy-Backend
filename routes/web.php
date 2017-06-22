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

Route::get('android', function () {
    return view('pages.android')->with(['metaTags' => true, 'pageTitle' => 'Selfy for Android']);
});

Route::get('/', function () {
    return view('pages.welcome')->with(['pageTitle' => 'Selfy', 'metaTags'=> false ]);
});

Route::post('/ajax/contact', 'App\UserController@contact');

Route::get('/facebook/link', 'App\UserController@confirm_facebook_link');

Route::group(['prefix' => 'admin', 'namespace' => 'Admin' , 'middleware' => 'web'], function (){
    Route::get('/login', [
        'as' => 'login',
        'uses' => 'LoginAdminController@formLogin'
    ]);
    Route::post('/login', [
        'as' => 'SelfyAdminLoginPost',
        'uses' => 'LoginAdminController@login'
    ]);
    Route::post('/login', [
        'as' => 'SelfyAdminLogout',
        'uses' => 'LoginAdminController@logout'
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
    Route::get('roles', ['uses' => 'RolesController@index', 'as' => 'SelfyAdminRoles']);
    Route::post('roles', ['uses' => 'RolesController@createRole', 'as' => 'SelfyAdminRolesCreate'] );
    Route::post('roles/edit/{id}', 'RolesController@edit');
    Route::post('roles/removeRole/{id}', 'RolesController@removeRole');
    Route::post('roles/rolePermissions/{id}', 'RolesController@rolePermissions');

    Route::get('permissions', 'PermissionsController@index');
    Route::post('permissions', 'PermissionsController@createPermission');
    Route::post('permissions/edit/{id}', 'PermissionsController@editPermission');
    Route::post('permissions/removePermission/{id}', 'PermissionsController@removePermission');
});