<?php

namespace App\Core;

class Validator
{
    protected $errors = [];

    public function validate(array $data, array $rules): array
    {
        foreach ($rules as $field => $rule) {
           foreach(explode("|", $rule) as $r) {
                if ($r === 'required' && empty($data[$field])) {
                    $this->errors[$field][] = "{$field} is required.";
                } elseif ($r === "numeric" && !is_numeric($data[$field])) {
                    $this->errors[$field][] = "{$field} must be numeric.";
                } elseif ($r === "string" && !is_string($data[$field])) {
                    $this->errors[$field][] = "{$field} must be a string.";
                } elseif ($r === "boolean" && !is_bool($data[$field])) {
                    $this->errors[$field][] = "{$field} must be a boolean.";
                } elseif ($r === 'email' && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "{$field} must be a valid email address.";
                }
            }
        }
        return $this->errors;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}

/*
// Example usage:
    $request = new Request();
    $validator = new Validator();
    $data = [
        'name' => $request->input('name'),
        'age' => $request->input('age'),
        'email' => $request->input('email')
    ];
    if ($errors = $validator->validate($data, [
        'name' => 'required|string',
        'age' => 'required|numeric',
        'email' => 'required|email'
    ])) {
        print_r($errors);
    } else {
        echo "Validation passed!";
    }
*/