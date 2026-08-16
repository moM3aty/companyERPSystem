<?php
// Path: app/Modules/POS/Shifts/Domain/PosShift.php

declare(strict_types=1);

namespace App\Modules\POS\Shifts\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: POS Shift
 * يمثل وردية الكاشير (فتح وإغلاق الصندوق). لا يمكن إجراء مبيعات بدون وردية مفتوحة.
 */
class PosShift extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'terminal_id'      => 'integer',
        'user_id'          => 'integer', // الكاشير
        'opened_at'        => 'string',
        'closed_at'        => 'string',
        'opening_amount'   => 'float', // العهدة المستلمة في بداية الوردية
        'closing_amount'   => 'float', // المبلغ الفعلي عند إغلاق الوردية
        'expected_amount'  => 'float', // المبلغ المتوقع بناءً على المبيعات (يحسب آلياً)
        'status'           => 'string', // 'open', 'closed'
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];

    public function isOpen(): bool
    {
        return $this->getAttribute('status') === 'open';
    }
}