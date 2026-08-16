<?php
// Path: app/Core/Bootstrap/ProviderRegistry.php

declare(strict_types=1);

namespace App\Core\Bootstrap;

use Exception;

/**
 * Enterprise Provider Registry
 * Manages the sequential registration and booting phases of all Service Providers.
 */
class ProviderRegistry
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected Application $app;

    /**
     * The providers that have been registered.
     *
     * @var array<ServiceProvider>
     */
    protected array $registeredProviders = [];

    /**
     * Indicates if the registry has been booted.
     *
     * @var bool
     */
    protected bool $booted = false;

    /**
     * ProviderRegistry constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register an array of Service Provider classes.
     *
     * @param array<string> $providers Array of class names.
     * @return void
     * @throws Exception
     */
    public function registerProviders(array $providers): void
    {
        foreach ($providers as $providerClass) {
            if (!class_exists($providerClass)) {
                throw new Exception("Service Provider [{$providerClass}] does not exist.");
            }

            /** @var ServiceProvider $providerInstance */
            $providerInstance = new $providerClass($this->app);
            
            if (!$providerInstance instanceof ServiceProvider) {
                throw new Exception("Class [{$providerClass}] must extend App\Core\Bootstrap\ServiceProvider.");
            }

            $providerInstance->register();
            $this->registeredProviders[] = $providerInstance;
        }
    }

    /**
     * Boot all registered Service Providers.
     * This must be called AFTER all providers have been registered.
     *
     * @return void
     */
    public function bootProviders(): void
    {
        if ($this->booted) {
            return;
        }

        foreach ($this->registeredProviders as $provider) {
            if (method_exists($provider, 'boot')) {
                // Dependency injection is supported in the boot method via the Container
                $this->app->call([$provider, 'boot']);
            }
        }

        $this->booted = true;
    }
}