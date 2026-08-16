<?php
// Path: app/Modules/Manufacturing/Routings/Domain/RoutingStep.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\Routings\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Routing Step
 * يمثل خطوة واحدة من خطوات التصنيع المرتبطة بمركز عمل معين (Work Center).
 */
class RoutingStep extends Entity
{
    protected array $casts = [
        'id'                     => 'integer',
        'routing_id'             => 'integer',
        'work_center_id'         => 'integer', // أين ستتم هذه الخطوة؟
        'step_number'            => 'integer', // الترتيب (1, 2, 3...)
        'operation_name'         => 'string',  // اسم العملية (تجميع، طلاء، فحص)
        'setup_time_minutes'     => 'float',   // وقت تجهيز الماكينة (يحدث مرة واحدة لكل Batch)
        'execution_time_minutes' => 'float',   // وقت تشغيل القطعة الواحدة
    ];
}