<?php

use Illuminate\Http\Request;
use App\Http\Middleware\UserAuthentication;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
define('API_HTTP_OK', 200);
define('API_HTTP_BAD_REQUEST', 400);
define('API_HTTP_UNAUTHORIZED', 401);
define('API_HTTP_FORBIDDEN', 403);
define('API_HTTP_NOT_FOUND', 404);
define('API_HTTP_FAILED_LOGIC', 422);
define('API_HTTP_SERVER_ERROR', 500);
define('STATUS_SUCCESS', 'success');
define('STATUS_ERROR', 'error');

const
    TRIP_STATUS_NEW = 1,
    TRIP_STATUS_CANCEL = 2,
    TRIP_STATUS_REJECT = 3,
    TRIP_STATUS_ACCEPT = 4,
    TRIP_STATUS_ARRIVED = 5,
    TRIP_STATUS_JOURNEY_COMPLETED = 6,
    TRIP_STATUS_COMPLETED = 7;

const 
    TYPE_USER   = 1,
    TYPE_DRIVER = 2,
    TYPE_ADMIN  = 3;
    
const
    OTP_INCORRECT = 203,
    SEND_OTP_ERROR = 204,
    MISSING_PARAMS = 300,
    EMAIL_EXIST = 401,
    EMAIL_OR_PASSWORD_INCORRECT = 409,
    PASSWORD_MISSING = 410,
    PASSWORD_INCORRECT = 411,
    EMAIL_DOES_NOT_EXIST = 412,
    LOGOUT_FAILED = 413,
    ACCOUNT_INACTIVE = 414,
    TOKEN_MISSING = 507,
    TOKEN_MISS_MATCH = 510,
    TRIP_NOT_PERMISSION = 421,
    PRICE_NOT_FOUND = 422,
    TRIP_CANCEL_OR_COMPLETE = 423;
    
const 
    STATUS_ACTIVE = 1,
    STATUS_INACTIVE = 0;    
    
const 
    VEHICLE_TYPE_FLAT_BED = 'flatbed',
    VEHICLE_TYPE_NORMAL = 'normal';    

if(isset($_SERVER['HTTP_ORIGIN'])) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
} else {
    header('Access-Control-Allow-Origin: *');
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, HEAD, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Auth-Token, token, X-Requested-With, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/v1/user/test', 'Api\TestController@test');
Route::get('/v1/test-sms', 'Api\TestController@testSMS');
//Route::post('/v1/user/register', 'Api\AccountController@register');
//Route::post('/v1/user/login', 'Api\AccountController@login');

//Route::group(['middleware' => ['api']], function () {
    Route::post('/v1/user/register', 'Api\AccountController@register');
    Route::post('/v1/user/login', 'Api\AccountController@login');
    Route::post('/v1/user/reset-password', 'Api\AccountController@forgotPassword');
    Route::any('/v1/user/get-otp', 'Api\AccountController@getOTP');
    Route::post('/v1/user/verify-otp', 'Api\AccountController@verifyOTP');
//});

Route::group(['middleware' => ['auth.api']], function () {
    Route::post('/v1/user/update-profile', 'Api\AccountController@updateProfile');
    Route::post('/v1/user/logout', 'Api\AccountController@logout');
    Route::post('/v1/user/rate-trip', 'Api\AccountController@rateTrip');

    Route::get('/v1/price/list', 'Api\PriceController@index');

    Route::post('/v1/trip/create', 'Api\TripController@create');
    Route::put('/v1/trip/update', 'Api\TripController@update');
    Route::get('/v1/trip/get-new-trip', 'Api\TripController@getNewTrip');
    Route::get('/v1/trip/get-my-trip', 'Api\TripController@getMyTrip');
    Route::post('/v1/trip/get-price', 'Api\TripController@getPriceByDistance');
    Route::get('/v1/trip/get-setting-time', 'Api\TripController@getSettingTime');
    Route::post('/v1/trip/update-location', 'Api\TripController@updateLocation');
    Route::post('/v1/trip/update-payment-status', 'Api\TripController@updatePaymentStatus');

    Route::post('/v1/feedback/send', 'Api\FeedbackController@create');
});