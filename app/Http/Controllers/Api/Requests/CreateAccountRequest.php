<?php

namespace Modules\Employee\Http\Requests;

use Modules\Core\Http\Requests\BaseFormRequest;

class CreateAccountRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'email'     => 'required|max:255|unique:account',
            'password'  => 'required|max:255',
            'phone'     => 'required|max:20|unique:account',
            'full_name' => 'required|max:255',
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'email.unique' => trans('employee::departments.validation.title.unique'),
            'email.required' => trans('employee::departments.validation.title.required'),
            'password.unique' => trans('employee::departments.validation.code.unique'),
            'password.required' => trans('employee::departments.validation.code.required'),
            'phone.required' => trans('employee::departments.validation.code.required'),
            'phone.unique' => trans('employee::departments.validation.code.unique'),
        ];
    }

}
