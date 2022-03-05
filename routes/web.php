<?php
use Illuminate\Support\Facades\Route;
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
    return view('samantha');
});
Route::get('/setup', 'SetupController@setup')->name('setup');
Route::post('/setup2', 'SetupController@setup2');
Route::get('/setup2', 'SetupController@setup2b');
Route::post('/setup3', 'SetupController@setup3');
Route::post('/setup4', 'SetupController@setup4');

Route::get('/main', 'MainController@main');
Route::get('/exchanges', 'SetupController@exchanges');
Route::post('/settings', 'SettingController@store')->name('settings.store');
Route::get('/settings', 'SettingController@create')->name('settings.create');

Route::get('/test', 'MainController@test');
