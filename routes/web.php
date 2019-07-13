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

Route::group(['middleware' => ['web']], function () {
    Route::group(['middleware' => ['auth']], function () {
        Route::group(['middleware' => ['role:superadmin']], function () {
            // Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
            Route::post('register/{role}', 'Auth\RegisterController@register')->where('role', 'admin|checker');
            Route::get('admins', 'SuperAdminController@getAdmins')->name('admins');
            Route::get('checkers', 'SuperAdminController@getCheckers')->name('checkers');
        });
        Route::group(['middleware' => ['role:superadmin,admin']], function () {
            Route::get('statistics', 'StatisticsController@getStatistics')->name('statistics');
        });
    });
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');

    Route::get('/', function () {
        return redirect('/login');
    });
    // Route::get('/home', 'HomeController@index')->name('home');
});


