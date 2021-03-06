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
    Route::get('fetchallplans', 'MainController@fetchallplans')->name('plans.fetchallplans');
    Route::get('cancelsubscription/{id}', 'MainController@cancelsubscription')->name('plans.cancelsubscription');
    Route::get('fetchusersubscriptions', 'MainController@fetchusersubscriptions')->name('plans.fetchusersubscriptions');

    Route::get('banktransfer', 'MainController@banktransfer')->name('plans.banktransfer');
    Route::get('createcharge', 'MainController@createcharge')->name('plans.createcharge');
    Route::get('createaccount', 'MainController@createaccount')->name('plans.createaccount');
    Route::get('createexternalaccount', 'MainController@createexternalaccount')->name('plans.createexternalaccount');
});




