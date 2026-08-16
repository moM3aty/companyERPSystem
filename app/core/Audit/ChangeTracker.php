<?php
// Path: app/Core/Audit/ChangeTracker.php

declare(strict_types=1);

namespace App\Core\Audit;

use App\Core\Models\BaseModel;

/**
 * Enterprise Change Tracker
 * المحرك الذي يقارن بين المصفوفات لاستخراج (الاختلافات) بدقة قبل تسجيلها في AuditLog.
 * يضمن عدم تسجيل حركات إذا لم يحدث تغيير فعلي على البيانات.
 */
class ChangeTracker
{
    /**
     * استخراج الفروقات بين القيم القديمة والجديدة لكيان معين.
     *
     * @param BaseModel $model الكيان الذي يستخدم (HasAudit Trait) بشكل ضمني عبر BaseModel
     * @return array مصفوفة تحتوي على المفاتيح ['old' => [...], 'new' => [...]]
     */
    public function calculateDiff(BaseModel $model): array
    {
        $oldValues = $model->getOriginal();
        $newValues = $model->toArray(); // Get current attributes

        $diffOld = [];
        $diffNew = [];

        // الحقول المستثناة من التدقيق (مثل وقت التحديث الذي يتغير تلقائياً)
        $ignoredFields = ['updated_at', 'created_at', 'deleted_at'];

        foreach ($newValues as $key => $newValue) {
            if (in_array($key, $ignoredFields, true)) {
                continue;
            }

            $oldValue = $oldValues[$key] ?? null;

            // إذا كانت القيمة مصفوفة (JSON Cast)، نقوم بمقارنتها كـ JSON لمنع أخطاء الـ Array to String
            if (is_array($newValue) || is_array($oldValue)) {
                if (json_encode($newValue) !== json_encode($oldValue)) {
                    $diffOld[$key] = $oldValue;
                    $diffNew[$key] = $newValue;
                }
                continue;
            }

            // مقارنة القيم العادية (Strict Comparison)
            if ($oldValue !== $newValue) {
                $diffOld[$key] = $oldValue;
                $diffNew[$key] = $newValue;
            }
        }

        return [
            'old' => $diffOld,
            'new' => $diffNew,
        ];
    }
}