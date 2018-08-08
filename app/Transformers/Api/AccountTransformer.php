<?php

namespace App\Transformers\Api;

use App\Transformers\Api\DriverTransformer;
use App\Transformers\Api\CoreResource;

class AccountTransformer extends CoreResource
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
            'full_name' => $this->full_name.'',
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar ?
                asset('upload'.DIRECTORY_SEPARATOR.'account'.DIRECTORY_SEPARATOR.$this->avatar) : '',
            'type'   => $this->type,
            'drivers' => $this->driverR ? new DriverTransformer($this->driverR) : '',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at.'',
            'token'      => $this->token.''
        ];

        return $data;
    }
}
