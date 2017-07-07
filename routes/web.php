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

Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'web'], function (){
    Route::get('/login', [
        'as' => 'SelfyAdminLogin',
        'uses' => 'LoginAdminController@formLogin'
    ]);
    Route::post('/login', [
        'as' => 'SelfyAdminLoginPost',
        'uses' => 'LoginAdminController@login'
    ]);
    Route::post('/logout', [
        'as' => 'SelfyAdminLogout',
        'uses' => 'LoginAdminController@logout'
    ]);

});

Route::group(['prefix' => 'admin', 'namespace' => 'Admin' , 'middleware' => ['App\Http\Middleware\AuthMiddleware', 'role:system-administrator|system-moderator']], function () {
   // Route::get('/seed_word_net', 'AdminController@seedWordNet');
   //Route::get('/seed_word_words', 'AdminController@seedWordWords');
    Route::get('/', [
        'as' => 'DashboardIndex',
        'uses' => 'AdminController@index'
    ]);

    Route::get('/play', [
        'as' => 'AdminPlay',
        'uses' => 'AdminController@play'
    ]);

    Route::get('/play/create', [
        'as' => 'CreatePlay',
        'uses' => 'AdminController@createPlay'
    ]);

    Route::post('/play/create', [
        'as' => 'CreatePlaySingleton',
        'uses' => 'AdminController@createPlaySingleton'
    ]);

    Route::get('/play/{playId}', [
        'as' => 'AdminPlaySingleton',
        'uses' => 'AdminController@playSingleton'
    ]);

    Route::post('/play/{playId}', [
        'as' => 'UpdatePlaySingleton',
        'uses' => 'AdminController@updatePlaySingleton'
    ]);

    Route::get('/play/{playId}/objects', [
        'as' => 'AdminPlayObjectsSingleton',
        'uses' => 'AdminController@managePlayObjects'
    ]);

    Route::post('/play/{playId}/generator', [
        'as' => 'AdminPlayObjectsGenerator',
        'uses' => 'AdminController@playGenerateObjects'
    ]);

    Route::post('/ajax/play/remove_object', [
        'as' => 'AdminPlayRemoveObject',
        'uses' => 'AdminController@removeObjectAssociation'
    ]);

    Route::post('/ajax/play/associate_object', [
        'as' => 'AdminPlayAssociateObject',
        'uses' => 'AdminController@associatePlayObject'
    ]);

    Route::post('/ajax/play/create_object', [
        'as' => 'CreatePlayObject',
        'uses' => 'AdminController@createPlayObject'
    ]);



    Route::post('/ajax/play/update_hashtag', [
        'as' => 'UpdatePlayHashtagSingleton',
        'uses' => 'AdminController@updatePlayHashtagSingleton'
    ]);

    Route::post('/ajax/challenge/toggle', [
        'as' => 'UpdateChallengeStatusSingleton',
        'uses' => 'AdminController@updateChallengeStatusSingleton'
    ]);

    Route::post('/ajax/hashtag/create', [
        'as' => 'CreateHashtag',
        'uses' => 'AdminController@createHashtag'
    ]);

    Route::get('roles', ['uses' => 'RolesController@index', 'as' => 'SelfyAdminRoles', 'middleware' => 'App\Http\Middleware\AclMiddleware:roles-crud']); //Ejemplo Roles Crud
   // Route::post('roles', ['uses' => 'RolesController@createRole', 'as' => 'SelfyAdminRolesCreate'] );
    Route::get('roles/create', [
        'as' => 'SelfyAdminRolesCreate',
        'uses' => 'RolesController@create',
        'middleware' => 'App\Http\Middleware\AclMiddleware:roles-crud'
    ]);//test

    Route::post('roles/create', [
        'as' => 'SelfyAdminRolesCreatePost',
        'uses' => 'RolesController@store',
        'middleware' => 'App\Http\Middleware\AclMiddleware:roles-crud'
    ]); //test

    Route::post('roles/{id}/edit', [
        'as' => 'SelfyAdminRolesUpdate',
        'uses' => 'RolesController@update',
        'middleware' => 'App\Http\Middleware\AclMiddleware:roles-crud'
    ]);//test

    Route::get('roles/{id}/edit', [
        'as' => 'SelfyAdminRolesEdit',
        'uses' => 'RolesController@edit',
        'middleware' => 'App\Http\Middleware\AclMiddleware:roles-crud'
    ]);//test

    Route::get('roles/{id}/permissions', [
        'as' => 'SelfyAdminRolesPermissions',
        'uses' => 'RolesController@permissions',
        'middleware' => 'App\Http\Middleware\AclMiddleware:roles-crud'
    ]);//test

    Route::put('roles/{id}/permissions', [
        'as' => 'SelfyAdminRolesPermissionsUpdate',
        'uses' => 'PermissionsController@permissionsUpdate',
        'middleware' => 'App\Http\Middleware\AclMiddleware:roles-crud'
    ]);//test

    Route::post('roles/removeRole/{id}', 'RolesController@removeRole');
    //Route::get('roles/rolePermissions/{id}', 'RolesController@rolePermissions');//test

    Route::get('permissions', 'PermissionsController@index');//test
    Route::get('permissions/create', [
        'as' => 'SelfyAdminPermissionsCreate',
        'uses' => 'PermissionsController@create',
        'middleware' => 'App\Http\Middleware\AclMiddleware:permissions-crud'
    ]); //test

    Route::post('permissions/create', [
        'as' => 'SelfyAdminPermissionsCreatePost',
        'uses' => 'PermissionsController@store',
        'middleware' => 'App\Http\Middleware\AclMiddleware:permissions-crud'
    ]); //test

    Route::get('permissions/{id}/edit', [
        'as' => 'SelfyAdminPermissionsEdit',
        'uses' => 'PermissionsController@edit',
        'middleware' => 'App\Http\Middleware\AclMiddleware:permissions-crud'
    ]);//test

    Route::post('roles/{id}/edit', [
        'as' => 'SelfyAdminPermissionsUpdate',
        'uses' => 'PermissionsController@update',
        'middleware' => 'App\Http\Middleware\AclMiddleware:permissions-crud'
    ]);//test

    Route::patch('permissions/removePermission/{id}', 'PermissionsController@removePermission');


});