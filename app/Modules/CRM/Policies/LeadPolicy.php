<?php
// Path: app/Modules/CRM/Policies/LeadPolicy.php

declare(strict_types=1);

namespace App\Modules\CRM\Policies;

use App\Core\Authorization\Policy;
use App\Core\Auth\AuthUser;

/**
 * Enterprise Policy: Lead
 * يحمي بيانات الـ Leads لضمان عدم تمكن مندوب مبيعات من رؤية Leads مسندة لمندوب آخر.
 */
class LeadPolicy extends Policy
{
    public function view(AuthUser $currentUser, array $lead): bool
    {
        // يجب أن يكون العميل المحتمل تابعاً لنفس الشركة
        if ($currentUser->companyId !== (int) $lead['company_id']) {
            return false;
        }

        // إذا كان الموظف مديراً فله الحق في رؤية الكل (يُحدد لاحقاً بالرول)، وإلا يجب أن يكون هو المندوب المسند إليه
        // للتوضيح: يُفترض أن הـ Gate قد سمح له بـ 'view_all_leads' أو 'view_own_leads'
        // هنا نتحقق من الملكية (Ownership)
        if (!empty($lead['assigned_to']) && $currentUser->id !== (int) $lead['assigned_to']) {
            return false; 
        }

        return true;
    }

    public function convert(AuthUser $currentUser, array $lead): bool
    {
        return $this->view($currentUser, $lead) && $lead['status'] !== 'converted';
    }
}