<?php
    return [
        'content header' => 'Users',
        'table' => [
            'stt' => 'Sl. No',
            'date' => 'Date',
            'trip_no' => 'Trip No',
            'driver_id' => 'Driver ID',
            'driver_name' => 'Driver Name',
            'driver_number' => 'Driver Number',
            'vehicle_number' => 'Vehicle Number',
            'company_name' => 'Company Name',
            'customer_name' => 'Customer Name',
            'customer_number' => 'Customer Number',
            'pick_up' => 'Pick Up Location',
            'drop_off' => 'Drop Off Location',
            'total_amount' => 'Total Amount',
            'trip_type' => 'Trip Type',
            'schedule_time' => 'Schedule Time',
            'trip_status' => 'Trip Status',
            'reason_cancel' => 'Reason For Cancel/ Reject',
            'paid_cash' => 'Paid by Cash',
            'paid_card' => 'Paid Card',
            'payment_status' => 'Payment Status',
            'rating' => 'Rating',
            'note' => 'Reason For Cancel/ Reject',
            'is_settlement' => 'Internal Settlement Status'
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
            'paid trip fail' => 'Paid Trip failed',            
            'paid trip success' => 'Paid Trip successfully',            
        ]
    ];