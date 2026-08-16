<?php
// Path: app/Modules/CRM/Routes/routes.php

declare(strict_types=1);

namespace App\Modules\CRM\Routes;

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;

use App\Modules\CRM\Leads\Http\Controllers\LeadController;
use App\Modules\CRM\Opportunities\Http\Controllers\OpportunityController;
use App\Modules\CRM\Customers\Http\Controllers\CustomerController;
use App\Modules\CRM\Activities\Http\Controllers\ActivityController;
use App\Modules\CRM\FollowUps\Http\Controllers\FollowUpController;

/**
 * Enterprise CRM Routes
 */
return static function (Router $router): void {
    
    $crm = new RouteGroup($router, [
        'prefix'     => 'api/v1/crm',
        'middleware' => ['api', 'auth', 'tenant']
    ]);

    $crm->group(function (RouteGroup $group) {
        
        // Leads
        $group->post('/leads', [LeadController::class, 'store']);
        
        // Opportunities (Sales Pipeline)
        $group->post('/opportunities', [OpportunityController::class, 'store']);
        
        // Customers & Contacts
        $group->get('/customers', [CustomerController::class, 'index']);
        $group->post('/customers', [CustomerController::class, 'store']);
        
        // Activities (Calls, Meetings, Emails)
        $group->post('/activities', [ActivityController::class, 'store']);
        
        // Follow Ups
        $group->post('/follow-ups', [FollowUpController::class, 'store']);
        $group->post('/follow-ups/{id}/complete', [FollowUpController::class, 'complete']);
    });
};