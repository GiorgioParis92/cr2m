<?php

namespace App\Validators;

class FormFieldValidator
{
    public function validate($type, $value, $required)
    {
        switch ($type) {
            case 'text':
                return $this->validateText($value, $required);
            case 'number':
                return $this->validateNumber($value, $required);
            case 'radio':
                return $this->validateRadio($value, $required);
            default:
                throw new \Exception("Unknown validation type: {$type}");
        }
    }

    private function validateText($value, $required)
    {
        if ($required && empty($value)) {
            return false;
        }
        return true;
    }

    private function validateNumber($value, $required)
    {
        if ($required && !is_numeric($value)) {
            return false;
        }
        return true;
    }

    private function validateRadio($value, $required)
    {
        if ($required && empty($value)) {
            return false;
        }
        return true;
    }
}
