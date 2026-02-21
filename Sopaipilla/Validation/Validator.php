<?php

declare(strict_types=1);

namespace Sopaipilla\Validation;

if (!function_exists('__')) {
    function __(string $text): string { return $text; }
}

class Validator
{
    private array $data = [];
    private array $rules = [];
    private array $errors = [];

    public function __construct(array $data = [], array $rules = [])
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? null;

            foreach ($fieldRules as $rule => $params) {
                $method = 'validate' . ucfirst($rule);

                if (method_exists($this, $method)) {
                    if (!$this->$method($value, $params)) {
                        $this->errors[$field][] = $this->getMessage($field, $rule, $params);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    protected function validateRequired($value, $params): bool
    {
        return $params ? !empty($value) : true;
    }

    protected function validateEmail($value, $params): bool
    {
        return !$params || empty($value) || filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateMin($value, $params): bool
    {
        if ($value === null || $value === '') {
            return true; // campo opcional no enviado, no aplica
        }
        return is_string($value) && \mb_strlen($value) >= $params;
    }

    protected function validateMax($value, $params): bool
    {
        if ($value === null || $value === '') {
            return true;
        }
        return is_string($value) && \mb_strlen($value) <= $params;
    }

    protected function validateNumeric($value, $params): bool
    {
        return !$params || is_numeric($value);
    }

    protected function validateRegex($value, $params): bool
    {
        return preg_match($params, (string) $value) === 1;
    }

    protected function validateIn($value, $params): bool
    {
        return in_array($value, $params, true);
    }

    protected function getMessage(string $field, string $rule, $params): string
    {
        $messages = [
            'required' => __("The field {$field} is required"),
            'email' => __("The field {$field} must be a valid email"),
            'min' => __("The field {$field} must have at least {$params} characters"),
            'max' => __("The field {$field} must have at most {$params} characters"),
            'numeric' => __("The field {$field} must be numeric"),
            'regex' => __("The field {$field} has an invalid format"),
            'in' => __("The field {$field} has an invalid value"),
        ];

        return $messages[$rule] ?? __("Validation failed for {$field}");
    }
}
