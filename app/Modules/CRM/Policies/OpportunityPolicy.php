<?php
// Path: app/Modules/CRM/Policies/OpportunityPolicy.php

declare(strict_types=1);

namespace App\Modules\CRM\Policies;

use App\Core\Authorization\Policy;
use App\Core\Auth\AuthUser;

/**
 * Enterprise Policy: Opportunity
 * يحدد من يحق له إغلاق الفرص البيعية (Won/Lost).
 */
class OpportunityPolicy extends Policy
{
    public function updateStage(AuthUser $currentUser, array $opportunity): bool
    {
        if ($currentUser->companyId !== (int) $opportunity['company_id']) {
            return false;
        }

        // الفرص المغلقة لا تتغير حالتها
        if (in_array($opportunity['stage'], ['closed_won', 'closed_lost'], true)) {
            return false;
        }

        return true;
    }
}