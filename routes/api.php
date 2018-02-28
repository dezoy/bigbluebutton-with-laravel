<?php

use Illuminate\Http\Request;

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

Route::post('register', 'AuthController@register');
Route::post('login',    'AuthController@login');
Route::post('recover',  'AuthController@recover');
Route::get('v1', function(){
    return response()->json(['foo'=>'bar']);
});

Route::group(['middleware' => ['jwt.auth']], function() {
    Route::get('logout', 'AuthController@logout');

    Route::post('refresh', 'AuthController@refresh');

    Route::post('v1', 'APIController@distributor');

    Route::get('test', function(){
        return response()->json(['foo'=>'bar']);
    });

});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
