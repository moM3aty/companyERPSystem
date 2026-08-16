<?php
// Path: app/Core/Validation/Rule.php

declare(strict_types=1);

namespace App\Core\Validation;

/**
 * Enterprise Validation Rule Interface
 * العقد الأساسي الذي يجب أن تلتزم به أي قاعدة تحقق مخصصة (Custom Rule).
 */
interface Rule
{
    /**
     * تقييم ما إذا كانت القيمة تمر من شرط التحقق أم لا.
     *
     * @param string $field اسم الحقل (مثال: email)
     * @param mixed $value القيمة المدخلة للحقل
     * @param array $data جميع البيانات المدخلة في الـ Request (مفيدة للقواعد المعتمدة على حقول أخرى)
     * @return bool إرجاع صحيح (true) إذا نجح التحقق، وخطأ (false) إذا فشل.
     */
    public function passes(string $field, mixed $value, array $data): bool;

    /**
     * رسالة الخطأ التي يجب عرضها في حالة فشل التحقق.
     *
     * @param string $field اسم الحقل
     * @return string
     */
    public function message(string $field): string;
}