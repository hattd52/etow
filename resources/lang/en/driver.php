<?php
    return [
        'content header' => 'Users',
        'table' => [
            'stt' => 'Sl. No',
            'id' => 'Driver ID',
            'driver_code' => 'Driver ID',
            'full_name' => 'Driver name',
            'avatar' => 'Driver Photo',
            'email' => 'Email ID',
            'phone' => 'Phone',
            'vehicle_type' => 'Type of Service',
            'vehicle_number' => 'Vehicle Number',
            'company' => 'Company Name',
            'emirate_id' => 'Emirates ID',
            'driving_license' => 'Driving License',
            'mulkiya' => 'Mulkiya',
            'is_online' => 'Driver Status',
            'trip_complete' => 'Completed Trips',
            'trip_cancel' => 'Canceled Trips',
            'trip_reject' => 'Rejected Trips',
            'trip_new' => 'Scheduled Trips',
            'trip_ongoing' => 'Ongoing Trips',
            'rate' => 'Avge Rating',
            'status' => 'Activate/ Deactivate',
            'action' => 'Edit/Delete'
        ],
        'validator' => [
            'full_name' => ['required' => 'The driver name field is required.']
        ],
        'message' => [
            'create success' => 'Create Driver successfully.',
            'create failed'  => 'Create Driver failed.',
            'update success' => 'Update Driver successfully.',
            'update failed'  => 'Update Driver failed.',
            'update status success' => 'Update status successfully',
            'update status fail' => 'Update status failed',
            'destroy success' => 'Delete driver successfully',
            'destroy fail' => 'Delete driver failed',            
        ]
    ];