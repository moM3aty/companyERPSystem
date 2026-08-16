<?php
// Path: app/Core/Bootstrap/Kernel.php

declare(strict_types=1);

namespace App\Core\Bootstrap;

use Throwable;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\JsonResponse;
use App\Core\Routing\Router;
use App\Core\Config\Config;
use App\Core\Monitoring\PerformanceMonitor;

/**
 * Enterprise HTTP Kernel
 * Update: Added Performance Monitoring call during termination.
 */
class Kernel
{
    protected Application $app;
    protected Router $router;

    public function __construct(Application $app, Router $router)
    {
        $this->app = $app;
        $this->router = $router;
    }

    public function handle(Request $request): Response
    {
        try {
            $this->app->boot();
            $response = $this->router->dispatch($request);
            return $this->prepareResponse($response);
        } catch (Throwable $e) {
            return $this->handleException($request, $e);
        }
    }

    protected function prepareResponse(mixed $response): Response
    {
        if ($response instanceof Response) return $response;
        if (is_array($response) || is_object($response)) return new JsonResponse($response);
        return new Response((string) $response);
    }

    protected function handleException(Request $request, Throwable $e): Response
    {
        /** @var Config $config */
        $config = $this->app->make(Config::class);
        $env = $config->get('app.env', 'production');
        
        $statusCode = (int) $e->getCode();
        if ($statusCode < 400 || $statusCode > 599) $statusCode = 500;

        $message = $env === 'development' ? $e->getMessage() : 'Internal Server Error';
        $trace = $env === 'development' ? $e->getTrace() : [];

        if ($request->ajax() || str_contains($request->server('HTTP_ACCEPT', ''), 'application/json')) {
            return new JsonResponse(['status' => 'error', 'message' => $message, 'code' => $statusCode, 'trace' => $trace], $statusCode);
        }

        return new Response("<h1>Error {$statusCode}</h1><p>{$message}</p>", $statusCode);
    }

    public function terminate(Request $request, Response $response): void
    {
        try {
            // Log performance metrics automatically at the end of the lifecycle
            if ($this->app->has(PerformanceMonitor::class)) {
                $monitor = $this->app->make(PerformanceMonitor::class);
                $monitor->record($request->uri());
            }
        } catch (Throwable $e) {
            // Ignore monitoring failures to not disrupt the final response
        }
    }
}