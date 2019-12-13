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

Route::post('/changePassword', 'HomeController@changePassword')->name('home');
Route::post('/createBook', 'BookController@createBook');
Route::post('/findBook', 'BookController@findBook');
Route::post('/updateBook', 'BookController@updateBook');
Route::post('/deleteBook', 'BookController@deleteBook');
Route::post('/upload', 'BookController@uploadFile');
Route::post('/searchData', 'BookController@searchData');
Route::post('/generateExcel', 'BookController@exportExcel');

