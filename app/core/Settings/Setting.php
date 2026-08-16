<?php
// Path: app/Core/Settings/Setting.php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Core\Models\Entity;

/**
 * Enterprise Setting Entity
 * يمثل سجل إعداد واحد في قاعدة البيانات.
 * الجداول المفترضة: (id, scope, scope_id, key, value, type, created_at, updated_at)
 */
class Setting extends Entity
{
    /**
     * تحديد نوع البيانات للحقول للتحويل التلقائي (Casting).
     *
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'scope_id' => 'integer',
        // الـ Value يتم تخزينها كـ Text أو JSON في الداتا بيز لتستوعب المصفوفات
        'value' => 'json', 
    ];


    public const SCOPE_GLOBAL = 'global';
    public const SCOPE_COMPANY = 'company';
    public const SCOPE_BRANCH = 'branch';
    public const SCOPE_USER = 'user';

    /**
     * الحصول على قيمة الإعداد بنوعها الأصلي.
     *
     * @return mixed
     */
    public function getTypedValue(): mixed
    {
        $value = $this->getAttribute('value');
        $type = $this->getAttribute('type');

        return match ($type) {
            'boolean', 'bool' => (bool) $value,
            'integer', 'int' => (int) $value,
            'float', 'double' => (float) $value,
            'array', 'json' => is_string($value) ? json_decode($value, true) : (array) $value,
            default => (string) $value,
        };
    }
}