<?php
// Path: app/Modules/CRM/Opportunities/Domain/OpportunityRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\CRM\Opportunities\Domain;

use App\Core\Contracts\RepositoryInterface;

interface OpportunityRepositoryInterface extends RepositoryInterface
{
    /**
     * تحديث مرحلة الفرصة البيعية (Sales Pipeline Stage).
     *
     * @param int $opportunityId
     * @param string $stage
     * @param int $probability
     * @return void
     */
    public function updateStage(int $opportunityId, string $stage, int $probability): void;
}