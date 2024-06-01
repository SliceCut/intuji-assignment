<?php

namespace App\Cores;

use App\Cores\Session;

class Validation
{

    private Request $request;
    private ErrorBag $error;
    private array $errors = [];

    public const RULE_REQUIRED = "required";
    public const RULE_SOMETIMES = "sometimes";
    public const RULE_EMAIL = "email";
    public const RULE_MATCH = "match";
    public const RULE_MIN = "min";
    public const RULE_MAX = "max";
    public const RULE_UNIQUE = "unique";
    public const RULE_FILE = "file";

    public const SESSION_OLD_VALUES = "session_old_values";
    public const SESSION_VALIDATION_ERROR = "session_error";

    public function __construct()
    {
        $this->request = new Request;
        $this->error = new ErrorBag;
    }

    public function rules(): array
    {
        return [];
    }

    public function validate()
    {
        $check = true;
        foreach ($this->rules() as $attribute => $rules) {

            $value = $this->request->{$attribute};
            foreach ($rules as $rule) {
                $ruleName = $rule;

                if (!is_string($ruleName)) {
                    $ruleName = $rule[0];
                }

                // if($attribute == "image") {
                //     var_dump($_FILES[$attribute]);
                //     exit;
                // }

                if ($ruleName == self::RULE_SOMETIMES) {
                    if (!$value) {
                        $check = false;
                    }
                }

                if ($ruleName == self::RULE_REQUIRED && (!$value && !isset($_FILES[$attribute])) && $check) {
                    $this->addError($attribute, self::RULE_REQUIRED);
                }

                if ($ruleName == self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL) && $check) {
                    $this->addError($attribute, self::RULE_EMAIL);
                }

                if ($ruleName == self::RULE_MIN && strlen($value) < $rule['min'] && $check) {
                    $this->addError($attribute, self::RULE_MIN, $rule);
                }

                if ($ruleName == self::RULE_MAX && strlen($value) > $rule['max'] && $check) {
                    $this->addError($attribute, self::RULE_MAX, $rule);
                }

                if ($ruleName == self::RULE_MATCH && $value != $this->request->{$rule['match']} && $check) {
                    $this->addError($attribute, self::RULE_MATCH, $rule);
                }

                if ($ruleName == self::RULE_FILE && $check) {
                    $file = $this->request->file($attribute);

                    if (!$file || (!$file['name'] ?? null)) {
                        $this->addError($attribute, self::RULE_FILE);
                    }
                }

                if ($ruleName == self::RULE_UNIQUE && $check) {
                    $model = new $rule['model'];
                    $exists = $model->where($rule['column'], $this->request->{$attribute})
                        ->when($rule['value'] ?? null, function ($callback, $value) {
                            return $callback->whereNotEqual('id', $value);
                        })
                        ->first();

                    if ($exists) {
                        $this->addError($attribute, self::RULE_UNIQUE, $rule);
                    }
                }
            }
        }

        if (count($this->errors) > 0) {

            /**
             * validation error message
             */
            $this->error->setErrors($this->errors);
            Session::delByKey(self::SESSION_VALIDATION_ERROR);
            Session::put(self::SESSION_VALIDATION_ERROR, $this->error);

            /**
             * Validaton old values
             */
            Session::delByKey(self::SESSION_OLD_VALUES);
            Session::put(self::SESSION_OLD_VALUES, $this->request->all());

            /**
             * redirect to previous url
             */
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    public function addError(string $attribute, string $rule, array $rules = [])
    {
        $message = $this->errorMessages()[$rule] ?? "";

        foreach ($rules as $key => $value) {
            $message = str_replace("{$key}", $value, $message);
        }

        $this->errors[$attribute][] = $message;
    }

    public function errorMessages()
    {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => "This field must be valid email address",
            self::RULE_MIN => 'Min length of this field should be {min}',
            self::RULE_MAX => 'MaX length of this field should be {max}',
            self::RULE_MATCH => 'This field must be same as {match}',
            self::RULE_UNIQUE => 'This field already exist',
            self::RULE_FILE => "This field must be file",
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFirstError(string $key): string
    {
        return $this->errors[$key][0] ?? "";
    }

    public function exist(string $key): bool
    {
        return (bool) ($this->errors[$key] ?? null);
    }
}
