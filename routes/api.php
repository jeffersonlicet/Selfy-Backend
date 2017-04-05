<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Auth not required */
Route::post('user/create', 'Api\AuthController@create');
Route::post('user/login', 'Api\AuthController@login');
Route::post('user/refresh', 'Api\AuthController@refresh');

/* Auth required */
Route::group(['middleware' => 'ApiAuth'], function () {

    Route::get('photo/borders/{photo_id}', 'Api\PhotoController@borders');
    Route::get('photo/bests/{user_id}', 'Api\PhotoController@best');
    Route::get('photo/report/{photo_id}', 'Api\PhotoController@report');

    Route::resource('photo', 'Api\PhotoController',
        ['only' => ['index','store','update','destroy','show']]);

    Route::resource('comment', 'Api\CommentController',
        ['only' => ['index' ,'store','update','destroy','show']]);

    Route::resource('like', 'Api\LikeController',
        ['only' => ['store','destroy','index']]);

    Route::resource('user', 'Api\UserController',
        ['only' => ['destroy','update', 'show']]);
});
