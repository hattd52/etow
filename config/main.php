<?php

define('DRIVER_ONLINE', 'online');
define('DRIVER_OFFLINE', 'offline');
define('FREE_DRIVER', 'free');
define('DRIVER_ON_TRIP', 'on_trip');

const
    TRIP_ON_GOING = 'on_going',
    TRIP_SCHEDULE = 'schedule',
    TRIP_COMPLETE = 'complete',
    TRIP_REJECT   = 'reject',
    TRIP_CANCEL   = 'cancel';

const
    SETTING_TIME_KM = 'TIME_KM',
    SETTING_TIME_BUFFER = 'TIME_BUFFER',
    SETTING_TIME_REQUEST_SCHEDULE = 'TIME_REQUEST_SCHEDULE',
    SETTING_TIME_ESTIMATE_ARRIVE = 'TIME_ESTIMATE_ARRIVE',
    SETTING_RADIUS_REQUEST = 'RADIUS_REQUEST';

const
    PAYMENT_STATUS_PENDING = 'pending',
    PAYMENT_STATUS_SUCCESS = 'success',
    PAYMENT_STATUS_FAIL = 'fail';

const
    TRIP_BY_USER = 'users',
    TRIP_BY_DRIVER = 'drivers';

const
    PAYMENT_DRIVER_PENDING = 'pending',
    PAYMENT_DRIVER_PAID = 'paid';
const 
    PAID = 1,
    UNPAID = 0;
const
    PAYMENT_METHOD_CASH = 'cash',
    PAYMENT_METHOD_CARD = 'card'; 

const
    GOOGLE_API_KEY = 'AIzaSyDLRA9JSryc2T-qy1EDzkkHaojkFLeqS8I';

return [
    'driver_status' => [
        'online' => 1,
        'offline' => 0
    ]
];
