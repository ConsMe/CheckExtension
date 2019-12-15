<?php

use App\Http\Middleware\CanAddEditCheckers;
use App\Http\Middleware\HasAccessToCheckers;
use App\Http\Middleware\HasAccessToCheckersTasks;

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
            Route::post('register/{role}', 'Auth\RegisterController@register')->where('role', 'admin');
            Route::get('admins', 'SuperAdminController@getAdmins')->name('admins');
            Route::post('admins/delete', 'SuperAdminController@deleteAdmin');
            Route::post('admins/addchecker', 'SuperAdminController@addChecker');
            Route::post('admins/toggle-checkers-access', 'SuperAdminController@toggleCheckersAccess');
            Route::post('admins/set-max-allowed-checkers', 'SuperAdminController@setMaxCheckers');
            Route::post('admins/removechecker', 'SuperAdminController@removeChecker');
        });
        Route::group(['middleware' => ['role:superadmin,admin']], function () {
            Route::get('statistics', 'StatisticsController@getStatistics')->name('statistics');
            Route::get('statistics/get', 'StatisticsController@getData');
        });
        Route::group(['middleware' => [HasAccessToCheckers::class]], function () {
            Route::get('checkers', 'SuperAdminController@getCheckers')->name('checkers');
            Route::get('checkers/{id}', 'CheckerController@getChecker')->where(['id' => '[\d]+']);
            Route::post('checkers/stop', 'SuperAdminController@stopChecker');
            Route::post('checkers/change-errors-count', 'SuperAdminController@changeErrorsCount');
        });
        Route::group(['middleware' => [CanAddEditCheckers::class]], function () {
            Route::post('checkers/delete', 'SuperAdminController@deleteChecker');
            Route::post('users/changepassword', 'SuperAdminController@changePassword');
            Route::post('register/{role}', 'Auth\RegisterController@register')->where('role', 'checker');
        });
        Route::group(['middleware' => [HasAccessToCheckersTasks::class]], function () {
            Route::post('checker/add', 'CheckerLkController@addTask');
            Route::post('checker/stopstart', 'CheckerLkController@stopStart');
            Route::post('checker/delete', 'CheckerLkController@deleteTask');
        });
        Route::group(['middleware' => ['role:checker']], function () {
            Route::get('checker/lk', 'CheckerLkController@lk')->name('lk');
            Route::get('checker/gettoken', 'CheckerLkController@getToken');
            Route::get('download/extension', 'CheckerLkController@download');
            Route::post('writelog', 'CheckerLkController@writeLog');
        });
        Route::get('/', 'HomeController@index');
    });
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('get-current-time', function () {
        return time();
    });
});

Route::post(env('TELEGRAM_BOT_TOKEN').'/webhook', 'TelegramController@webhook')->name('telegram');
