<?php
// Path: app/Core/Integrations/IntegrationManager.php

declare(strict_types=1);

namespace App\Core\Integrations;

/**
 * Enterprise Integration Manager (Facade)
 * الواجهة المركزية التي تتعامل معها الكنترولرز للوصول لأي أداة من أدوات الربط.
 */
class IntegrationManager
{
    public readonly ApiClient $client;
    public readonly OAuthClient $oauth;
    public readonly WebhookManager $webhooks;
    public readonly SyncManager $sync;
    public readonly MappingManager $mapper;


    public function __construct(
        ApiClient $client,
        OAuthClient $oauth,
        WebhookManager $webhooks,
        SyncManager $sync,
        MappingManager $mapper
    ) {
        $this->client = $client;
        $this->oauth = $oauth;
        $this->webhooks = $webhooks;
        $this->sync = $sync;
        $this->mapper = $mapper;
    }
}