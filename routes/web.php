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

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/get-avatar/{fileName}', 'Controller@getAvatar');

#Login
Route::get('login', ['as' => 'login', 'uses' => 'AuthController@getLogin']);
Route::post('login', 'AuthController@postLogin');
#forgot
Route::get('forgot-password', ['as' => 'forgot-password', 'uses' => 'AuthController@forgotPassword']);
Route::post('forgot', ['as' => 'forgot', 'uses' => 'AuthController@postForgotPassword']);
Route::get('reset-password', ['as' => 'reset-password', 'uses' => 'AuthController@getResetPassword']);
Route::post('reset', ['as' => 'reset', 'uses' => 'AuthController@postResetPassword']);

Route::group(['middleware' => ['auth']], function () {
    # index
    Route::get('/',  ['middleware' => 'auth', 'uses' => 'AuthController@index']);
    #dashboard
    Route::get('dashboard',  ['as' => 'dashboard', 'uses' => 'AuthController@dashBoard']);
    # Logout
    Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@getLogout']);
    
    //==================== Users=====================
    Route::get('/users', [
        'as' => 'user.index',
        'uses' => 'UserController@index',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::post('/ajax/user/search', [
        'as' => 'ajax.user.search',
        'uses' => 'Ajax\AccountController@search',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::post('/ajax/user/update-status', [
        'as' => 'ajax.user.update_status',
        'uses' => 'Ajax\AccountController@updateStatus',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::delete('/user/delete/{id}', [
        'as' => 'user.destroy',
        'uses' => 'UserController@destroy',
        //'middleware' => 'can:user.index'
    ]);

    //==================== Drivers=====================
    Route::get('/drivers', [
        'as' => 'driver.index',
        'uses' => 'DriverController@index',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::post('/ajax/drivers/search', [
        'as' => 'ajax.driver.search',
        'uses' => 'Ajax\DriverController@search',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::get('/drivers/create', [
        'as' => 'driver.create',
        'uses' => 'DriverController@create',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::post('/drivers/store', [
        'as' => 'driver.store',
        'uses' => 'DriverController@store',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::get('/drivers/edit/{driver}', [
        'as' => 'driver.edit',
        'uses' => 'DriverController@edit',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::post('/drivers/update/{driver}', [
        'as' => 'driver.update',
        'uses' => 'DriverController@update',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::delete('/drivers/delete/{id}', [
        'as' => 'driver.delete',
        'uses' => 'DriverController@destroy',
        //'middleware' => 'can:user.index'
    ]);
    Route::get('/drivers/{type}', [
        'as' => 'driver.by_type',
        'uses' => 'DriverController@indexByType',
        //'middleware' => 'can:employee.employees.index'
    ]);

    //==================== Trips =====================
    Route::get('/trips', [
        'as' => 'trip.index',
        'uses' => 'TripController@index',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::post('/ajax/trips/search', [
        'as' => 'ajax.trip.search',
        'uses' => 'Ajax\TripController@search',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::get('/users/trip/{user_id}', [
        'as' => 'trip.by_user',
        'uses' => 'TripController@byUser',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::get('/users/trip/{user_id}/{type}', [
        'as' => 'trip.by_user_type',
        'uses' => 'TripController@typeByUser',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::post('/ajax/trips/map', [
        'as' => 'ajax.trip.map',
        'uses' => 'Ajax\TripController@map',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::post('/ajax/trips/get-marker', [
        'as' => 'ajax.trip.get_marker',
        'uses' => 'Ajax\TripController@getMarker',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::get('/drivers/trip/{driver_id}/{type}', [
        'as' => 'trip.by_driver_type',
        'uses' => 'TripController@typeByDriver',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::post('/ajax/trips/paid-for-driver', [
        'as' => 'ajax.trip.paid_for_driver',
        'uses' => 'Ajax\TripController@paidTrip',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::get('/trips/{type}', [
        'as' => 'trip.by_type',
        'uses' => 'TripController@indexByType',
        //'middleware' => 'can:employee.employees.index'
    ]);
    //==================== Setting =====================
    Route::get('/settings', [
        'as' => 'setting.index',
        'uses' => 'PriceController@index',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::post('/ajax/setting/search', [
        'as' => 'ajax.setting.search',
        'uses' => 'Ajax\PriceController@search',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::get('/settings/update-price', [
        'as' => 'setting.update_price',
        'uses' => 'PriceController@updatePrice',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::post('/settings/update', [
        'as' => 'setting.update',
        'uses' => 'PriceController@update',
        //'middleware' => 'can:employee.employees.index'
    ]);

    //==================== Feedback =====================
    Route::get('/feedbacks', [
        'as' => 'feedback.index',
        'uses' => 'FeedbackController@index',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::post('/ajax/feedback/search', [
        'as' => 'ajax.feedback.search',
        'uses' => 'Ajax\FeedbackController@search',
        //'middleware' => 'can:employee.employees.index'
    ]);
    Route::delete('/feedbacks/delete/{id}', [
        'as' => 'feedback.delete',
        'uses' => 'FeedbackController@destroy',
        //'middleware' => 'can:user.index'
    ]);
});