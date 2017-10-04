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

    Route::get('/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    Route::get('/oldseed/users/{page}', 'AdminController@oldUsersSeeder');
    Route::get('/oldseed/followers/{page}', 'AdminController@seedFollowers');
    Route::get('/oldseed/photos', 'AdminController@seedPhotos');
    Route::get('/oldseed/likes', 'AdminController@seedLikes');
    Route::get('/oldseed/comments', 'AdminController@seedComments');


    Route::get('/meli', 'AdminController@meliDashboard');
    Route::get('/meli/{targetId}/products', 'AdminController@meliProducts');
    Route::get('/meli/create/target', [
        'as' => 'MeliCreateTarget',
        'uses' => 'AdminController@meliCreateTargetForm'
    ]);

    Route::post('/meli/create/target', [
        'as' => 'MeliCreateTargetPost',
        'uses' => 'AdminController@meliCreateTarget'
    ]);

    Route::get('/', [
        'as' => 'DashboardIndex',
        'uses' => 'AdminController@index'
    ]);

    Route::get('/play', [
        'as' => 'AdminPlay',
        'uses' => 'AdminController@play'
    ]);

    Route::get('/places', [
        'as' => 'AdminPlaces',
        'uses' => 'AdminController@places'
    ]);

    Route::post('/ajax/place/create/spot', [
        'as' => 'CreateSpot',
        'uses' => 'AdminController@createSpot'
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
});


Route::get('user/password/reset/{token}', [
    'as' => 'password.reset',
    'uses' => 'App\ForgotPasswordController@showResetForm'
]);

Route::get('user/password_changed', function()
{
    return __('app.password_changed');
});

Route::post('user/password/reset', [
    'as' => 'password.change',
    'uses' => 'App\ForgotPasswordController@reset'
]);