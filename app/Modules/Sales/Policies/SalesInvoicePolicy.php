<?php
// Path: app/Modules/Sales/Policies/SalesInvoicePolicy.php

declare(strict_types=1);

namespace App\Modules\Sales\Policies;

use App\Core\Authorization\Policy;
use App\Core\Auth\AuthUser;

/**
 * Enterprise Policy: Sales Invoice
 * يحمي الفواتير المالية المعتمدة من أي تلاعب غير مصرح به.
 */
class SalesInvoicePolicy extends Policy
{
    public function update(AuthUser $currentUser, array $invoice): bool
    {
        if ($currentUser->companyId !== (int) $invoice['company_id']) {
            return false;
        }

        // الفواتير المرحلة (Posted) أو الملغية أو المدفوعة لا يمكن تعديلها إطلاقاً
        $immutableStatuses = ['posted', 'paid', 'voided'];
        if (in_array($invoice['status'], $immutableStatuses, true)) {
            return false;
        }

        return true;
    }
}