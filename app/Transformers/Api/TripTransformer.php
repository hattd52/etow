<?php

namespace App\Transformers\Api;

use App\Transformers\Api\DriverTransformer;
use App\Transformers\Api\CoreResource;

class TripTransformer extends CoreResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'driver_id' => $this->driver_id.'',
            'pick_up' => $this->pick_up.'',
            'drop_off' => $this->drop_off.'',
            'is_schedule'   => $this->is_schedule.'',
            'pickup_date'   => $this->pickup_date.'',
            'vehicle_type' => $this->vehicle_type.'',
            'price' => $this->price,
            'note' => $this->note.'',
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at.'',
            'user' => $this->userR ? new AccountTransformer($this->userR) : '',
            'driver' => $this->driverR ? new AccountTransformer($this->driverR) : ''
        ];

        return $data;
    }
}
