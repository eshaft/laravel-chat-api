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

Route::group(['middleware' => ['api']], function () {
    Route::post('/signup', 'AuthController@signup');
    Route::post('/login', 'AuthController@login');
    Route::get('/logout', 'AuthController@logout');

    Route::get('/users', 'UserController@index');
    Route::get('/users/me', 'UserController@me');
    Route::post('/users/me', 'UserController@update');
    Route::get('/users/{user}', 'UserController@show');

    Route::get('/chats', 'ChatController@index');
    Route::post('/chats', 'ChatController@store');
    Route::get('/chats/my', 'ChatController@my');
    Route::get('/chats/{chat}', 'ChatController@show');
    Route::delete('/chats/{chat}', 'ChatController@destroy');
    Route::get('/chats/{chat}/join', 'ChatController@join');
    Route::get('/chats/{chat}/leave', 'ChatController@leave');

    Route::post('/chats/{chat}', 'MessageController@store');
});




