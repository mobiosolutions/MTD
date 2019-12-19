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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::post('/password/change', 'UserController@changePassword');

Route::post('/createUser', 'UserController@createUser');
Route::post('/findUser', 'UserController@findUser');
Route::get('/getUsers', 'UserController@getUsers');
Route::post('/updateUser/{id}', 'UserController@updateUser');
Route::post('/deleteUser/{id}', 'UserController@deleteUser');

Route::post('/upload', 'BookController@uploadFile');
Route::post('/searchData', 'BookController@searchData');
Route::post('/generateExcel', 'BookController@exportExcel');

