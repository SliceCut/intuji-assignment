<?php

namespace App\Cores;

use App\Cores\Session;
use DateTime;
use Exception;

class Validation extends Request
{

    private Request $request;
    private ErrorBag $error;
    private array $errors = [];

    public const RULE_REQUIRED = "required";
    public const RULE_SOMETIMES = "sometimes";
    public const RULE_STRING = "string";
    public const RULE_INTEGER = "integer";
    public const RULE_EMAIL = "email";
    public const RULE_MATCH = "match";
    public const RULE_MIN = "min";
    public const RULE_MAX = "max";
    public const RULE_UNIQUE = "unique";
    public const RULE_FILE = "file";
    public const RULE_DATE = "date";
    public const RULE_DATE_TIME = "datetime";
    public const RULE_AFTER = "after";
    public const RULE_AFTER_OR_EQUAL = "after_or_equal";
    public const RULE_BEFORE_TODAY = "before_today";
    public const RULE_AFTER_TODAY = "after_today";
    public const RULE_DATE_FORMAT = "date_format:{attribute}";

    public const SESSION_OLD_VALUES = "session_old_values";
    public const SESSION_VALIDATION_ERROR = "session_error";

    public function __construct()
    {
        $this->request = new Request;
        $this->error = new ErrorBag;
        $this->merge($this->request->all());
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

                if ($ruleName == self::RULE_REQUIRED && !$value) {
                    $this->addError($attribute, self::RULE_REQUIRED);
                    continue;
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

                if ($ruleName == self::RULE_STRING && $value) {
                    if (!is_string($value)) {
                        $this->addError($attribute, self::RULE_STRING);
                    }
                }

                if ($ruleName == self::RULE_INTEGER && $value) {
                    if (filter_var($value, FILTER_VALIDATE_INT) == false) {
                        $this->addError($attribute, self::RULE_INTEGER);
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

                if ($value && strpos($ruleName, "date_format:") !== false) {
                    $format = extractDateFormat($ruleName);
                    if (!$format) {
                        throw new Exception("Invalid date_format validation rule.");
                    }
                    $dateTime = DateTime::createFromFormat($format, $value);

                    // Check if the date was created successfully and if the format matches exactly
                    $isValid = $dateTime && $dateTime->format($format) === $value;
                    if (!$isValid) {
                        $this->addError($attribute, self::RULE_DATE_FORMAT);
                    }
                }


                if ($value && ($ruleName == self::RULE_DATE || $ruleName == self::RULE_DATE_TIME)) {
                    if (!isDateOrDateTime($value)) {
                        $this->addError($attribute, $ruleName);
                    }
                }

                if ($value && strpos($ruleName, "after") !== false) {
                    $extractKey = extractStringAfter($ruleName, "after");

                    if (!$extractKey) {
                        throw new Exception("Invalid after: validation rule.");
                    }

                    $extractKeyValue = $this->request->$extractKey;

                    if ($extractKeyValue) {
                        if (strtotime($value) <= strtotime($extractKeyValue)) {
                            $this->addError($attribute, "after", [
                                ":key" => $extractKey
                            ]);
                        }
                    }
                }

                if ($value && strpos($ruleName, "after_or_equal") !== false) {
                    $extractKey = extractStringAfter($ruleName, "after_or_equal");

                    if (!$extractKey) {
                        throw new Exception("Invalid after_or_equal: validation rule.");
                    }

                    $extractKeyValue = $this->request->$extractKey;

                    if ($extractKeyValue) {
                        if (strtotime($value) < strtotime($extractKeyValue)) {
                            $this->addError($attribute, "after_or_equal", [
                                ":key" => $extractKey
                            ]);
                        }
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
            Session::put(self::SESSION_VALIDATION_ERROR, serialize($this->error));

            /**
             * Validaton old values
             */
            Session::delByKey(self::SESSION_OLD_VALUES);
            Session::put(self::SESSION_OLD_VALUES, $this->request->all());

            if ($_SERVER["REQUEST_METHOD"] == 'GET') {
                return redirect(strtok(currentUrl(), '?'));
            }

            return redirectBack();
        }
    }

    public function addError(string $attribute, string $rule, array $rules = [])
    {
        $message = $this->errorMessages()[$rule] ?? "";

        foreach ($rules as $key => $value) {
            $message = str_replace("{$key}", $value, $message);
        }

        $message = str_replace(":attribute", $attribute, $message);

        $this->errors[$attribute][] = $message;
    }

    public function errorMessages()
    {
        return [
            self::RULE_REQUIRED => 'This field :attribute is required',
            self::RULE_STRING => 'This field :attribute must be string',
            self::RULE_INTEGER => 'This field :attribute must be integer',
            self::RULE_EMAIL => "This field :attribute must be valid email address",
            self::RULE_MIN => 'Min length of this field should be {min}',
            self::RULE_MAX => 'MaX length of this field should be {max}',
            self::RULE_MATCH => 'This field :attribute must be same as {match}',
            self::RULE_UNIQUE => 'This field :attribute already exist',
            self::RULE_FILE => "This field :attribute must be file",
            self::RULE_DATE_FORMAT => "The field :attribute is invalid date_format",
            self::RULE_AFTER => "The field :attribute must be after :key",
            self::RULE_AFTER_OR_EQUAL => "The field :attribute must be after_or_equal :key",
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
