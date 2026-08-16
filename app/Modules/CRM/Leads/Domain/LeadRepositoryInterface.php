<?php
// Path: app/Modules/CRM/Leads/Domain/LeadRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\CRM\Leads\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: CRM Lead
 */
interface LeadRepositoryInterface extends RepositoryInterface
{
    /**
     * تحديث حالة العميل المحتمل.
     *
     * @param int $leadId
     * @param string $status
     * @return void
     */
    public function updateStatus(int $leadId, string $status): void;
}