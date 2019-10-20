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

Route::resource('main','MainController');
Route::resource('customer','CustomerController');
Route::resource('product','ProductController');

Route::delete('customerDeleteSelected', 'CustomerController@deleteSelected');
Route::delete('productDeleteSelected', 'ProductController@deleteSelected');
Route::delete('clearStockHistory', 'ProductStockHistoryController@clearHistory');