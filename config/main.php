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
    SETTING_TIME_BUFFER = 'TIME_BUFFER';

const
    PAYMENT_STATUS_PENDING = 'pending',
    PAYMENT_STATUS_SUCCESS = 'success',
    PAYMENT_STATUS_FAIL = 'fail';

const
    GOOGLE_API_KEY = 'AIzaSyAIfGZf9EAxX7rQ7nam9xwtboW74pGZU-o';

return [
    'driver_status' => [
        'online' => 1,
        'offline' => 0
    ]
];
