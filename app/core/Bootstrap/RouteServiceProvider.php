<?php
// Path: app/Core/Bootstrap/RouteServiceProvider.php

declare(strict_types=1);

namespace App\Core\Bootstrap;

use App\Core\Routing\Router;

/**
 * Enterprise Route Service Provider
 * Loads web and API routes into the core Router instance.
 */
class RouteServiceProvider
{
    protected Container $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function register(): void
    {
        if (!$this->app->has(Router::class)) {
            $this->app->singleton(
                Router::class,
                function () {
                    return new Router();
                }
            );
        }
    }

    public function boot(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        $basePath = $this->app->basePath();

        /**
         * =========================================================================
         * 1. Load Primary Web Routes
         * =========================================================================
         */
        $webRoutesPath = $basePath
            . DIRECTORY_SEPARATOR
            . 'routes'
            . DIRECTORY_SEPARATOR
            . 'web.php';

        if (file_exists($webRoutesPath)) {
            $webRegistrar = require $webRoutesPath;

            /**
             * Support both:
             * - Callable route registrars
             * - Legacy route files that directly use $router
             */
            if (is_callable($webRegistrar)) {
                $webRegistrar($router);
            }
        }

        /**
         * =========================================================================
         * 2. Load API Routes
         * =========================================================================
         */
        $apiRoutesPath = $basePath
            . DIRECTORY_SEPARATOR
            . 'routes'
            . DIRECTORY_SEPARATOR
            . 'api.php';

        if (file_exists($apiRoutesPath)) {
            $apiRegistrar = require $apiRoutesPath;

            if (is_callable($apiRegistrar)) {
                $apiRegistrar($router);
            }
        }

        /**
         * =========================================================================
         * 3. Dynamic Module Routes Discovery
         * =========================================================================
         */
        $modulesPath = $basePath
            . DIRECTORY_SEPARATOR
            . 'app'
            . DIRECTORY_SEPARATOR
            . 'Modules';

        if (is_dir($modulesPath)) {
            $modules = @scandir($modulesPath);

            if ($modules !== false) {
                foreach ($modules as $module) {
                    if ($module === '.' || $module === '..') {
                        continue;
                    }

                    $moduleRouteFile = $modulesPath
                        . DIRECTORY_SEPARATOR
                        . $module
                        . DIRECTORY_SEPARATOR
                        . 'Routes'
                        . DIRECTORY_SEPARATOR
                        . 'routes.php';

                    if (!file_exists($moduleRouteFile)) {
                        continue;
                    }

                    $routeRegistrar = require $moduleRouteFile;

                    if (is_callable($routeRegistrar)) {
                        $routeRegistrar($router);
                    }
                }
            }
        }
    }
}