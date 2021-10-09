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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('auth')->group(function () {
    Route::post('/register', 'App\Http\Controllers\UserController@register');
    Route::post('/login', 'App\Http\Controllers\UserController@login');
    Route::post('/reset-password', 'App\Http\Controllers\UserController@passwordReset');
    Route::post('/change-password', 'App\Http\Controllers\UserController@passwordChange');
});
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::prefix('events')->group(function () {
        Route::get('', 'App\Http\Controllers\EventController@getAll');
        Route::get('{event_id}', 'App\Http\Controllers\EventController@getById');
        Route::patch('{event_id}', 'App\Http\Controllers\EventController@update');
        Route::delete('{event_id}', 'App\Http\Controllers\EventController@delete');
        Route::post('', 'App\Http\Controllers\EventController@create');
    });
    Route::prefix('calendars')->group(function () {
        Route::post('', 'App\Http\Controllers\CalendarController@create');
        Route::patch('{calendar_id}', 'App\Http\Controllers\CalendarController@update');
        Route::delete('{calendar_id}', 'App\Http\Controllers\CalendarController@delete');
        Route::get('', 'App\Http\Controllers\CalendarController@getAll');
        Route::post('{calendar_id}/invites', 'App\Http\Controllers\InviteController@create');
        Route::patch('{calendar_id}/invites', 'App\Http\Controllers\InviteController@update');
        Route::delete('{calendar_id}/invites', 'App\Http\Controllers\InviteController@delete');
    });
    Route::prefix('users')->group(function () {
        Route::get('/my_account', function () {
            return auth()->user();
        });
        Route::get('','App\Http\Controllers\UserController@getAllWithoutMe');
    });
});

