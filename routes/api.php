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

    Route::resource('photo', 'Api\PhotoController',
        ['only' => ['store','update','destroy','show']]);
    Route::get('photos', 'Api\PhotoController@index');

    Route::get('photo/borders/{photo_id}', 'Api\PhotoController@borders');
    Route::get('photo/bests/{user_id}', 'Api\PhotoController@bests');
    Route::get('photo/report/{photo_id}', 'Api\PhotoController@report');

    Route::resource('comment', 'Api\CommentController',
        ['only' => ['store','update','destroy']]);
    Route::get('comments', 'Api\CommentController@index');

    Route::resource('like', 'Api\LikeController',
        ['only' => ['store','destroy']]);

    Route::get('likes', 'Api\LikeController@index');
    Route::post('user/avatar', 'Api\UserController@avatar');

    Route::post('user/face', 'Api\UserController@face');
    Route::post('user/update', 'Api\UserController@update');
    Route::post('user/firebase', 'Api\UserController@firebase');
    Route::post('user/follow', 'Api\UserController@follow');
    Route::post('user/unfollow', 'Api\UserController@unfollow');
    Route::get('user/followers', 'Api\UserController@followers');
    Route::get('user/following', 'Api\UserController@following');
    Route::get('user/me', 'Api\UserController@me');
    Route::get('user/challenges', 'Api\UserController@challenges');

    Route::resource('user', 'Api\UserController',
        ['only' => ['destroy', 'show']]);

    Route::get('challenges/todo', 'Api\ChallengesController@todo');
    Route::get('challenges/completed', 'Api\ChallengesController@completed');
    Route::resource('challenge', 'Api\ChallengesController',
        ['only' => ['show','store','destroy']]);

    Route::get('notifications', 'Api\NotificationsController@index');





});
