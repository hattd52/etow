<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Core\Traits\Api\ApiReponseFormatTrait;

class BaseApiFormRequest extends FormRequest
{

    /**
     * Format request data
     */
    public function getFormatted($key, $def_value = null)
    {
        $value = $this->get($key);

        //Formats go here

        return $value ? $value : $def_value;
    }

    /**
     * Get all data formatted
     */
    public function allWithFormat()
    {
        $formatted = [];

        foreach ($this->all() as $key => $value) {
            $formatted[$key] = $this->getFormatted($key, $value);
        } 

        return $formatted;
    }

    /**
     * 
     */
    public function onlyWithFormat($arrKeys = [])
    {
        $formatted = [];
        $data = $this->only($arrKeys);

        foreach ($data as $key => $value) {
            $formatted[$key] = $this->getFormatted($key, $value);
        } 

        return $formatted;
    }
}
