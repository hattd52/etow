<?php 

namespace Modules\Core\Http\Requests\Validators;

class TimestampAfter {

    public function validate($attribute, $value, $parameters, $validator)
    {
        $data = $validator->getData();

        if (!isset($parameters[0])) return false;

        $compared_field = $parameters[0];
        $compared_value = $data[$compared_field];

        if ($value <= $compared_value) {
            return false;
        }

        return true;
    }

    public function message($message, $attribute, $rule, $parameters)
    {
        $field = $attribute;

        if (!isset($parameters[0])) {
            return sprintf("%s validation rule requires one parameter. None has given.", $rule);
        }

        $compared_field = $parameters[0];

        return sprintf("The %s must be a time after %s", str_replace('_', ' ', $field), str_replace('_', ' ', $compared_field));
    }
}