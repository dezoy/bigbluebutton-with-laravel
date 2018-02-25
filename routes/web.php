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

Route::get('/', function () {
    //return view('welcome');
	return redirect("/meeting/list");
});


Route::get( '/meetings',		'BigBlueButtonController@getMeetings')->name('meetings');
Route::get( '/meeting/add',		'BigBlueButtonController@addMeeting')->name('meeting.add');
Route::get( '/meeting/list',	'BigBlueButtonController@listMeeting')->name('meeting.list');
Route::post('/meeting/create',	'BigBlueButtonController@createMeeting')->name('meeting.create');
Route::get( '/meeting/join/{name}/{password}/{meetingID}',	'BigBlueButtonController@joinMeeting')->name('meeting.join');
Route::get( '/meeting/info/{password}/{meetingID}',			'BigBlueButtonController@getMeetingInfo')->name('meeting.info');
Route::get( '/meeting/close/{password}/{meetingID}',		'BigBlueButtonController@closeMeeting')->name('meeting.close');
Route::get( '/meeting/recordings',	'BigBlueButtonController@getRecordings')->name('meeting.recordings');
Route::get( '/meeting/recording/delete/{recordId}',	'BigBlueButtonController@deleteRecordings')->name('meeting.recording.delete');
Route::get( '/meeting/running',		'BigBlueButtonController@isMeetingRunning')->name('meeting.running');


Route::get('user/verify/{verification_code}', 'AuthController@verifyUser');
Route::get('password/reset/{token}', 		  'Auth\ResetPasswordController@showResetForm')->name('password.request');
Route::post('password/reset', 				  'Auth\ResetPasswordController@postReset')->name('password.reset');
