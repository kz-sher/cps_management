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
	if (Auth::check()){
		return view('main');
	}
    return view('auth.login');
});

Auth::routes([ 'register' => false ]);

Route::resource('main','MainController');
Route::resource('customer','CustomerController');
Route::resource('product','ProductController');
Route::resource('supplier','SupplierController');

Route::delete('customerDeleteSelected', 'CustomerController@deleteSelected');
Route::delete('productDeleteSelected', 'ProductController@deleteSelected');
Route::delete('supplierDeleteSelected', 'SupplierController@deleteSelected');
Route::delete('clearStockHistory', 'ProductStockHistoryController@clearHistory');

Route::patch('importProduct/{id}', 'ProductController@importProduct');