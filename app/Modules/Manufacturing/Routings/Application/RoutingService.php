<?php
// Path: app/Modules/Manufacturing/Routings/Application/RoutingService.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\Routings\Application;

use App\Modules\Manufacturing\Routings\Infrastructure\RoutingRepository;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;

class RoutingService
{
    protected RoutingRepository $routingRepo;
    protected TransactionManager $transaction;

    public function __construct(RoutingRepository $routingRepo, TransactionManager $transaction)
    {
        $this->routingRepo = $routingRepo;
        $this->transaction = $transaction;
    }

    public function createRouting(array $headerData, array $stepsData, int $companyId): int
    {
        return $this->transaction->execute(function () use ($headerData, $stepsData, $companyId) {
            
            if (empty($stepsData)) {
                throw new BusinessException("A routing must contain at least one step.");
            }

            $headerData['company_id'] = $companyId;
            $headerData['is_active']  = 1;
            $headerData['created_at'] = date('Y-m-d H:i:s');

            $routingId = $this->routingRepo->create($headerData);

            // ترتيب الخطوات تصاعدياً لضمان التسلسل الصحيح للإنتاج
            usort($stepsData, fn($a, $b) => $a['step_number'] <=> $b['step_number']);

            $this->routingRepo->bulkInsertSteps($routingId, $stepsData);

            return $routingId;
        });
    }
}