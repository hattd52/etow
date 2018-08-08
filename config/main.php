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
return [
    'driver_status' => [
        'online' => 1,
        'offline' => 0
    ]
];
