<?php
// Path: app/Core/Routing/RouteGroup.php

declare(strict_types=1);

namespace App\Core\Routing;

use Closure;

/**
 * Enterprise Route Group
 *
 * Provides an interface for grouping routes and applying
 * prefixes and middlewares to multiple routes.
 */
class RouteGroup
{
    protected Router $router;

    protected string $prefix = '';

    protected array $middleware = [];

    /**
     * RouteGroup constructor.
     *
     * @param Router $router
     * @param array $attributes
     */
    public function __construct(Router $router, array $attributes = [])
    {
        $this->router = $router;

        if (isset($attributes['prefix'])) {
            $this->prefix = '/' . trim(
                (string) $attributes['prefix'],
                '/'
            );
        }

        if (isset($attributes['middleware'])) {
            $this->middleware = array_values(
                (array) $attributes['middleware']
            );
        }
    }

    /**
     * Execute the group callback.
     *
     * @param Closure $callback
     * @return void
     */
    public function group(Closure $callback): void
    {
        $callback($this);
    }

    /**
     * Register GET route.
     */
    public function get(string $uri, mixed $action): Route
    {
        return $this->addRoute(
            ['GET', 'HEAD'],
            $uri,
            $action
        );
    }

    /**
     * Register POST route.
     */
    public function post(string $uri, mixed $action): Route
    {
        return $this->addRoute(
            'POST',
            $uri,
            $action
        );
    }

    /**
     * Register PUT route.
     */
    public function put(string $uri, mixed $action): Route
    {
        return $this->addRoute(
            'PUT',
            $uri,
            $action
        );
    }

    /**
     * Register DELETE route.
     */
    public function delete(string $uri, mixed $action): Route
    {
        return $this->addRoute(
            'DELETE',
            $uri,
            $action
        );
    }

    /**
     * Add a route to the underlying Router.
     *
     * @param array|string $methods
     * @param string $uri
     * @param mixed $action
     * @return Route
     */
    protected function addRoute(
        array|string $methods,
        string $uri,
        mixed $action
    ): Route {
        $uri = trim($uri, '/');

        if ($this->prefix !== '') {
            $fullUri = $this->prefix . '/' . $uri;
        } else {
            $fullUri = '/' . $uri;
        }

        $fullUri = '/' . trim($fullUri, '/');

        $route = $this->router->addRoute(
            $methods,
            $fullUri,
            $action
        );

        if (!empty($this->middleware)) {
            $route->middleware($this->middleware);
        }

        return $route;
    }
}