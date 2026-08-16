<?php
// Path: app/Core/Validation/Validator.php

declare(strict_types=1);

namespace App\Core\Validation;

use App\Core\Validation\ValidationResult;
use App\Core\Validation\ValidationException;

/**
 * Enterprise Validator Engine
 * المحرك الرئيسي لتحليل قواعد التحقق النصية أو الكائنات، وتطبيقها على البيانات.
 */
class Validator
{
    /**
     * البيانات المراد التحقق منها.
     *
     * @var array
     */
    protected array $data;

    /**
     * القواعد المطلوب تطبيقها.
     *
     * @var array
     */
    protected array $rules;

    /**
     * Validator constructor.
     *
     * @param array $data
     * @param array $rules
     */
    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }


    /**
     * تنفيذ عملية التحقق.
     *
     * @return ValidationResult
     */
    public function validate(): ValidationResult
    {
        $result = new ValidationResult();

        foreach ($this->rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? null;
            $rulesArray = $this->parseRules($fieldRules);
            
            $fieldHasErrors = false;

            foreach ($rulesArray as $rule) {
                // تجاوز باقي القواعد إذا كان الحقل فارغاً وغير مطلوب
                if ($value === null || $value === '') {
                    if (!$this->hasRequiredRule($rulesArray)) {
                        break;
                    }
                }

                $error = $this->applyRule($field, $value, $rule);
                
                if ($error !== null) {
                    $result->addError($field, $error);
                    $fieldHasErrors = true;
                    // التوقف عن فحص باقي القواعد لنفس الحقل إذا فشلت قاعدة
                    break; 
                }
            }

            if (!$fieldHasErrors && array_key_exists($field, $this->data)) {
                $result->addValidatedData($field, $value);
            }
        }

        return $result;
    }

    /**
     * التحقق ورمي استثناء (Exception) مباشرة إذا فشلت العملية.
     * الدالة الأكثر استخداماً داخل الـ Controllers.
     *
     * @return array البيانات الموثقة فقط (Validated Data)
     * @throws ValidationException
     */
    public function validateOrFail(): array
    {
        $result = $this->validate();

        if ($result->fails()) {
            throw new ValidationException($result->getErrors());
        }

        return $result->getValidatedData();
    }


    /**
     * تحليل القواعد سواء كانت نصوص (مفصولة بـ |) أو مصفوفات.
     *
     * @param string|array $rules
     * @return array
     */
    protected function parseRules(string|array $rules): array
    {
        if (is_array($rules)) {
            return $rules;
        }

        return explode('|', $rules);
    }

    /**
     * فحص هل يحتوي الحقل على قاعدة "required".
     *
     * @param array $rules
     * @return bool
     */
    protected function hasRequiredRule(array $rules): bool
    {
        foreach ($rules as $rule) {
            if (is_string($rule) && str_starts_with($rule, 'required')) {
                return true;
            }
        }
        return false;
    }


    /**
     * تطبيق قاعدة تحقق واحدة على حقل.
     *
     * @param string $field
     * @param mixed $value
     * @param mixed $rule
     * @return string|null يُرجع رسالة الخطأ إذا فشل، أو null إذا نجح.
     */
    protected function applyRule(string $field, mixed $value, mixed $rule): ?string
    {
        // 1. تطبيق القواعد المخصصة (Custom Rules) التي تلتزم بالـ Rule Interface
        if ($rule instanceof Rule) {
            if (!$rule->passes($field, $value, $this->data)) {
                return $rule->message($field);
            }
            return null;
        }

        // 2. تطبيق القواعد الأساسية المدمجة بالنظام
        if (is_string($rule)) {
            $parameters = [];
            
            // فحص هل القاعدة تحتوي على متغيرات (مثال: max:255)
            if (str_contains($rule, ':')) {
                [$ruleName, $paramString] = explode(':', $rule, 2);
                $parameters = explode(',', $paramString);
                $rule = $ruleName;
            }

            return match ($rule) {
                'required' => $this->validateRequired($field, $value),
                'email' => $this->validateEmail($field, $value),
                'numeric' => $this->validateNumeric($field, $value),
                'integer' => $this->validateInteger($field, $value),
                'string' => $this->validateString($field, $value),
                'min' => $this->validateMin($field, $value, $parameters),
                'max' => $this->validateMax($field, $value, $parameters),
                default => null, // تجاوز القاعدة إذا كانت غير معرفة هنا (يمكن ربطها مستقبلاً)
            };
        }

        return null;
    }


    protected function validateRequired(string $field, mixed $value): ?string
    {
        if ($value === null || (is_string($value) && trim($value) === '') || (is_array($value) && count($value) === 0)) {
            return "The {$field} field is required.";
        }
        return null;
    }

    protected function validateEmail(string $field, mixed $value): ?string
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return "The {$field} must be a valid email address.";
        }
        return null;
    }

    protected function validateNumeric(string $field, mixed $value): ?string
    {
        if (!is_numeric($value)) {
            return "The {$field} must be a number.";
        }
        return null;
    }

    protected function validateInteger(string $field, mixed $value): ?string
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            return "The {$field} must be an integer.";
        }
        return null;
    }

    protected function validateString(string $field, mixed $value): ?string
    {
        if (!is_string($value)) {
            return "The {$field} must be a string.";
        }
        return null;
    }

    protected function validateMin(string $field, mixed $value, array $parameters): ?string
    {
        $min = (float) ($parameters[0] ?? 0);
        
        if (is_numeric($value) && $value < $min) {
            return "The {$field} must be at least {$min}.";
        }
        if (is_string($value) && mb_strlen($value) < $min) {
            return "The {$field} must be at least {$min} characters.";
        }
        
        return null;
    }

    protected function validateMax(string $field, mixed $value, array $parameters): ?string
    {
        $max = (float) ($parameters[0] ?? 0);
        
        if (is_numeric($value) && $value > $max) {
            return "The {$field} must not be greater than {$max}.";
        }
        if (is_string($value) && mb_strlen($value) > $max) {
            return "The {$field} must not be greater than {$max} characters.";
        }
        
        return null;
    }
}