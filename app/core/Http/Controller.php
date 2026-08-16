<?php
// Path: app/Core/Http/Controller.php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Http\JsonResponse;
use App\Core\Http\RedirectResponse;
use App\Core\Http\Response;

/**
 * Enterprise Base Controller
 * Provides standardized helper methods and middleware registration for all system controllers.
 */
abstract class Controller
{
    /**
     * The middleware registered on the controller.
     *
     * @var array
     */
    protected array $middleware = [];

    /**
     * Register middleware on the controller.
     *
     * @param string|array $middleware
     * @return self
     */
    public function middleware(string|array $middleware): self
    {
        foreach ((array) $middleware as $m) {
            $this->middleware[] = $m;
        }

        return $this;
    }

    /**
     * Get the middleware assigned to the controller.
     *
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * Return a standardized JSON response.
     * Highly useful for API endpoints and DataTables AJAX calls.
     *
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function json(mixed $data, int $status = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * Return a Redirect response.
     *
     * @param string $url
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
    protected function redirect(string $url, int $status = 302, array $headers = []): RedirectResponse
    {
        return new RedirectResponse($url, $status, $headers);
    }

    /**
     * Return a standard HTML Response.
     * (E.g., used when returning rendered Views/Templates).
     *
     * @param mixed $content
     * @param int $status
     * @param array $headers
     * @return Response
     */
    protected function response(mixed $content = '', int $status = 200, array $headers = []): Response
    {
        return new Response($content, $status, $headers);
    }
}