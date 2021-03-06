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
Route::post('user/exists', 'Api\AuthController@sync_facebook');
Route::post('user/facebook_create', 'Api\AuthController@create_facebook');
Route::post('user/facebook_link', 'Api\AuthController@link_facebook');
Route::post('user/facebook_confirm', 'Api\AuthController@confirm_facebook');
Route::post('user/request_password', 'Api\AuthController@resetPassword');


/* Auth required */
Route::group(['middleware' => ['ApiAuth']], function () {
    Route::get('photo/borders/{photo_id}', 'Api\PhotoController@borders');
    Route::get('photo/bests/{user_id}', 'Api\PhotoController@bests');
    Route::post('photo/report', 'Api\PhotoController@report');
    Route::get('photo/clothes/{photo_id}', 'Api\PhotoController@clothes');
    Route::resource('photo', 'Api\PhotoController', ['only' => ['store','update','destroy','show']]);
    Route::get('photos', 'Api\PhotoController@index');
    Route::get('photos/hashtag_search', 'Api\PhotoController@hashtag_search');
    Route::get('photos/hashtag', 'Api\PhotoController@photos_hashtag');
    Route::get('photos/recent', 'Api\PhotoController@recent');
    Route::resource('comment', 'Api\CommentController', ['only' => ['store','update','destroy']]);
    Route::get('comments', 'Api\CommentController@index');
    Route::resource('like', 'Api\LikeController', ['only' => ['store','destroy']]);
    
    Route::post('invitation/accept', 'Api\InvitationController@accept');
    Route::post('invitation/decline', 'Api\InvitationController@decline');
    Route::post('invitation/remove', 'Api\InvitationController@remove');
    Route::post('invitation/create', 'Api\InvitationController@create');

    Route::get('invitations', 'Api\InvitationController@index');

    Route::get('likes', 'Api\LikeController@index');
    Route::post('user/avatar', 'Api\UserController@avatar');

    Route::get('users/fb_suggestions', 'Api\UserController@facebook_suggestion');
    Route::get('users/suggestions', 'Api\UserController@suggestions');

    Route::get('users/duo', 'Api\UserController@duo');
    Route::get('users/search', 'Api\UserController@search');
    Route::get('search/mix', 'Api\UserController@searchMix');

    Route::get('users/search_mentions', 'Api\UserController@search_mention_suggestion');
    Route::get('users/featured', 'Api\UserController@featured');
    Route::get('conversation/user/{userId}', 'Api\UserController@getConversationByUser');
    Route::get('conversations', 'Api\UserController@getChats');

    Route::delete('conversation/user/{userId}', 'Api\UserController@deleteConversationByUser');

    Route::get('conversation/id/{conversationId}', 'Api\UserController@getConversationById');

    Route::post('user/message', 'Api\UserController@sendMessage');
    Route::post('user/message/delete', 'Api\UserController@deleteMessage');

    Route::post('user/update_username', 'Api\UserController@update_username');
    Route::post('user/block', 'Api\UserController@block');
    Route::post('user/unblock', 'Api\UserController@unblock');
    Route::post('user/update_challenges', 'Api\UserController@update_challenges');
    Route::post('user/update_facebook_token', 'Api\UserController@update_facebook_token');
    Route::post('user/face', 'Api\UserController@face');
    Route::post('user/update', 'Api\UserController@update');
    Route::post('user/update_creation', 'Api\UserController@update_creation');

    Route::post('user/firebase', 'Api\UserController@firebase');
    Route::post('user/push_token/update', 'Api\UserController@pushTokenUpdate');

    Route::post('user/follow', 'Api\UserController@follow');
    Route::post('user/unfollow', 'Api\UserController@unfollow');
    Route::post('user/exists_implicit', 'Api\AuthController@sync_facebook_implicit');

    Route::get('user/followers', 'Api\UserController@followers');
    Route::get('user/following', 'Api\UserController@following');
    Route::get('user/me', 'Api\UserController@me');
    Route::get('user/challenges', 'Api\UserController@challenges');

    Route::resource('user', 'Api\UserController', ['only' => ['destroy', 'show']]);

    Route::get('challenges/nearby', 'Api\ChallengesController@nearby');

    Route::post('challenge/accept', 'Api\ChallengesController@accept');
    Route::post('challenge/decline', 'Api\ChallengesController@decline');
    Route::post('challenge/remove', 'Api\ChallengesController@remove');

    Route::get('challenge/top_photos', 'Api\ChallengesController@top_photos');
    Route::get('challenge/recent_photos', 'Api\ChallengesController@recent_photos');

    Route::get('hashtags/top', 'Api\PhotoController@popular_hashtags');

    Route::resource('challenge', 'Api\ChallengesController',['only' => ['show']]);
    Route::get('notifications', 'Api\NotificationsController@index');
});
