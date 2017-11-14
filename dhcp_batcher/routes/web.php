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
    return view('auth/login');
})->middleware(['guest']);

Auth::routes();

Route::group(['middleware' => 'auth'], function() {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/flush','HomeController@flush');
    Route::get("/configuration","ConfigurationController@index");

    Route::group(['prefix' => 'dhcp_servers'], function() {
        Route::get("/","DhcpServerController@index");
        Route::get("/create","DhcpServerController@create");
        Route::patch("/{dhcp_server}/reset","DhcpServerController@resetPassword");
        Route::post("/","DhcpServerController@store");
        Route::delete("/{dhcp_server}","DhcpServerController@destroy");
    });
});


