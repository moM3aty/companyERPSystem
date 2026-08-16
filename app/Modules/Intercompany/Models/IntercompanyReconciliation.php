<?php
// File 1: app/Modules/Intercompany/Models/IntercompanyReconciliation.php
declare(strict_types=1);

namespace App\Modules\Intercompany\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Intercompany Reconciliation
 * يمثل وثيقة التسوية والمطابقة بين حسابات شركتين شقيقتين داخل المجموعة.
 */
class IntercompanyReconciliation extends BaseModel
{
    use HasTimestamps;

    protected array $casts = [
        'id'                     => 'integer',
        'period_id'              => 'integer',
        'company_a_id'           => 'integer', // الشركة الدائنة (المصدرة للفاتورة)
        'company_b_id'           => 'integer', // الشركة المدينة (المستلمة)
        'total_ar_company_a'     => 'float',   // إجمالي الذمم المسجلة في أ
        'total_ap_company_b'     => 'float',   // إجمالي الالتزامات المسجلة في ب
        'variance_amount'        => 'float',   // الفارق (يجب أن يكون صفراً في الحالة المثالية)
        'status'                 => 'string',  // 'draft', 'matched', 'has_variance', 'resolved'
        'reconciliation_date'    => 'string',
        'created_by'             => 'integer',
    ];
}