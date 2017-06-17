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
    return view('pages.welcome')->with(['pageTitle' => 'Selfy', 'metaTags'=> true ]);
});

Route::post('/ajax/contact', 'App\UserController@contact');

Route::get('/facebook/link', 'App\UserController@confirm_facebook_link');
