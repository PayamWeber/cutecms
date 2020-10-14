<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group( [
    'prefix' => 'v1',
], function () {
    Route::group( [
        'prefix' => 'auth',
    ], function () {
        Route::get( 'login', 'Api\Auth\SigninupController@login' )->middleware('throttle:15,1');
    } );

    Route::group( [
        'middleware' => [ 'auth:sanctum' ],
        'prefix' => 'user',
    ], function () {
//        Route::post( 'update_profile', 'Api\User\UserController@updateProfile' );
    } );

    Route::group( [
        'middleware' => [ 'auth:sanctum' ],
        'prefix' => 'post',
    ], function () {
        Route::get( '/', 'Api\Post\PostsController@index' );
    } );

    Route::group( [
        'middleware' => [ 'auth:sanctum' ],
    ], function () {
        Route::resource( '/task', 'Api\Task\TaskController' );
    } );
} );

Route::post( '/admins/media', 'Admin\Media\MediaController@store' )->name( 'media.store' );
Route::delete( '/admins/media/{id}', 'Admin\Media\MediaController@destroy' )->name( 'media.destroy' );
