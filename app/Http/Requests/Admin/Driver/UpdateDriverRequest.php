<?php

namespace App\Http\Requests\Admin\Driver;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDriverRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //'email' => 'required|email',
            //'password' => 'required',
            'full_name' => 'required',
            'vehicle_type' => 'required',
            'vehicle_number' => 'required',
            //'driver_license' => 'required'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'full_name.required' => trans('driver.validator.full_name.required')
        ];
    }    
}
