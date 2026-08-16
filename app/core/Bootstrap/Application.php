<?php
// Path: app/Core/Bootstrap/Application.php

declare(strict_types=1);

namespace App\Core\Bootstrap;

use Exception;

/**
 * Enterprise Application Core
 * Extends the Container to act as the central registry and lifecycle manager.
 */
class Application extends Container
{
    /**
     * The base path for the ERP installation.
     *
     * @var string
     */
    protected readonly string $basePath;

    /**
     * Indicates if the application has been bootstrapped before.
     *
     * @var bool
     */
    protected bool $hasBeenBootstrapped = false;

    /**
     * The loaded service providers.
     *
     * @var array<ServiceProvider>
     */
    protected array $serviceProviders = [];

    /**
     * Indicates if the application has "booted".
     *
     * @var bool
     */
    protected bool $isBooted = false;

    /**
     * Create a new Application instance.
     *
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '\/');
        $this->registerBaseBindings();
        $this->registerBasePaths();
    }

    /**
     * Register the basic bindings into the container.
     *
     * @return void
     */
    protected function registerBaseBindings(): void
    {
        static::setInstance($this);
        $this->instance('app', $this);
        $this->instance(Container::class, $this);
        $this->instance(self::class, $this);
    }

    /**
     * Bind all of the application paths in the container.
     *
     * @return void
     */
    protected function registerBasePaths(): void
    {
        $this->instance('path.base', $this->basePath);
        $this->instance('path.app', $this->basePath . DIRECTORY_SEPARATOR . 'app');
        $this->instance('path.config', $this->basePath . DIRECTORY_SEPARATOR . 'config');
        $this->instance('path.storage', $this->basePath . DIRECTORY_SEPARATOR . 'storage');
        $this->instance('path.public', $this->basePath . DIRECTORY_SEPARATOR . 'public');
    }

    /**
     * Get the base path of the application.
     *
     * @param string $path
     * @return string
     */
    public function basePath(string $path = ''): string
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Register a service provider with the application.
     *
     * @param ServiceProvider|string $provider
     * @return ServiceProvider
     * @throws Exception
     */
    public function register(ServiceProvider|string $provider): ServiceProvider
    {
        if (is_string($provider)) {
            $provider = $this->resolveProvider($provider);
        }

        $provider->register();
        $this->serviceProviders[] = $provider;

        if ($this->isBooted) {
            $this->bootProvider($provider);
        }

        return $provider;
    }

    /**
     * Resolve a service provider instance from the class name.
     *
     * @param string $provider
     * @return ServiceProvider
     * @throws Exception
     */
    protected function resolveProvider(string $provider): ServiceProvider
    {
        return new $provider($this);
    }

    /**
     * Boot the application's service providers.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->isBooted) {
            return;
        }

        foreach ($this->serviceProviders as $provider) {
            $this->bootProvider($provider);
        }

        $this->isBooted = true;
    }

    /**
     * Boot the given service provider.
     *
     * @param ServiceProvider $provider
     * @return void
     */
    protected function bootProvider(ServiceProvider $provider): void
    {
        if (method_exists($provider, 'boot')) {
            $this->call([$provider, 'boot']);
        }
    }

    /**
     * Determine if the application has booted.
     *
     * @return bool
     */
    public function isBooted(): bool
    {
        return $this->isBooted;
    }

    /**
     * Run the application (Handle HTTP Request or CLI command).
     * This is a placeholder for the actual request lifecycle entry point.
     *
     * @return void
     */
    public function run(): void
    {
        $this->boot();
        // Integration with HTTP Router/Kernel will happen here
    }
}