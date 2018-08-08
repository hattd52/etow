<?php

namespace App\Transformers\Api;

use App\Transformers\Api\CoreResource;

class DriverTransformer extends CoreResource
{
    public function toArray($request)
    {
        $data = [
            //'id' => $this->id,
            'user_id' => $this->user_id.'',
            'user_name' => $this->userR ? $this->userR->full_name : '',
            'vehicle_type' => $this->vehicle_type.'',
            'vehicle_number' => $this->vehicle_number.'',
            'company_name'   => $this->company_name.'',
            'is_online'      => $this->is_online.'',
            'driver_code'    => $this->driver_code,
            'driver_license' => $this->driver_license ? 
                asset('upload'.DIRECTORY_SEPARATOR.'driver'.DIRECTORY_SEPARATOR.$this->driver_license) : '',
            'emirate_id'     => $this->emirate_id ? 
                asset('upload'.DIRECTORY_SEPARATOR.'driver'.DIRECTORY_SEPARATOR.$this->emirate_id) : '',
            'mulyika'        => $this->mulkiya ? 
                asset('upload'.DIRECTORY_SEPARATOR.'driver'.DIRECTORY_SEPARATOR.$this->mulkiya) : ''
        ];

        return $data;
    }
}
