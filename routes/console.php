<?php
// Path: routes/console.php

declare(strict_types=1);

/**
 * Enterprise Console Routes / Scheduler Definitions
 */

global $scheduler;

// Process Outbox Messages (Event Driven Architecture backbone)
$scheduler->job(\App\Core\Outbox\OutboxProcessor::class)
          ->everyMinute()
          ->withoutOverlapping()
          ->description('Process pending outbox messages and publish domain events.');

// Escalate Pending Approvals
$scheduler->job(\App\Core\Workflow\Approval\EscalationManager::class)
          ->hourly()
          ->description('Escalate delayed approval requests based on SLA limits.');