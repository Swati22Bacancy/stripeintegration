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
    return view('welcome');
});

Auth::routes();

Route::group(['middleware' => 'auth'], function() {
    Route::get('main', 'MainController@show');
    Route::get('plans', 'MainController@index')->name('plans.index');
    Route::get('plan/{plan}', 'MainController@show')->name('plans.show');
    Route::post('subscription', 'MainController@create')->name('subscription.create');
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('fetchinvoices', 'MainController@fetchinvoices')->name('plans.invoices');
});




