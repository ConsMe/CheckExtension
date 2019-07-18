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
    Route::group(['middleware' => ['forceLogout', 'auth']], function () {
        Route::group(['middleware' => ['role:superadmin']], function () {
            // Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
            Route::post('register/{role}', 'Auth\RegisterController@register')->where('role', 'admin|checker');
            Route::get('admins', 'SuperAdminController@getAdmins')->name('admins');
            Route::post('admins/delete', 'SuperAdminController@deleteAdmin');
            Route::post('admins/addchecker', 'SuperAdminController@addChecker');
            Route::post('admins/removechecker', 'SuperAdminController@removeChecker');
            Route::get('checkers', 'SuperAdminController@getCheckers')->name('checkers');
            Route::post('checkers/delete', 'SuperAdminController@deleteChecker');
            Route::post('checkers/stop', 'SuperAdminController@stopChecker');
            Route::post('users/changepassword', 'SuperAdminController@changePassword');
        });
        Route::group(['middleware' => ['role:superadmin,admin']], function () {
            Route::get('statistics', 'StatisticsController@getStatistics')->name('statistics');
            Route::get('statistics/get', 'StatisticsController@getData');
        });
        Route::group(['middleware' => ['role:checker']], function () {
            Route::get('checker/lk', 'CheckerLkController@lk')->name('lk');
            Route::get('checker/gettoken', 'CheckerLkController@getToken');
            Route::get('download/extension', 'CheckerLkController@download');
            Route::post('checker/add', 'CheckerLkController@addTask');
            Route::post('checker/stopstart', 'CheckerLkController@stopStart');
            Route::post('checker/delete', 'CheckerLkController@deleteTask');
            Route::post('writelog', 'CheckerLkController@writeLog');
        });
        Route::get('/', 'HomeController@index');
    });
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');

    // Route::get('/home', 'HomeController@index')->name('home');
});

Route::post(env('TELEGRAM_BOT_TOKEN').'/webhook', 'TelegramController@webhook')->name('telegram');
