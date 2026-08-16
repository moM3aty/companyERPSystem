<?php
// Path: app/Modules/Sales/Subscription/Http/Controllers/SubscriptionController.php

declare(strict_types=1);

namespace App\Modules\Sales\Subscription\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Sales\Subscription\Application\SubscriptionService;
use App\Modules\Sales\Subscription\Http\Requests\StoreSubscriptionRequest;

class SubscriptionController extends Controller
{
    protected SubscriptionService $service;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        SubscriptionService $service,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->service = $service;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreSubscriptionRequest $validator): JsonResponse
    {
        $this->gate->authorize('sales', 'subscriptions', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $subscriptionId = $this->service->setupSubscription($validatedData, $companyId, $userId);

        return ApiResponse::created(['subscription_id' => $subscriptionId], 'Subscription created successfully. Invoices will be generated automatically.');
    }
}