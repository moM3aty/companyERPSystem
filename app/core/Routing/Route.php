<?php
// Path: app/Core/Routing/Route.php

declare(strict_types=1);

namespace App\Core\Routing;

/**
 * Enterprise Route Definition
 * Represents a single registered route and its properties.
 */
class Route
{
    /**
     * The HTTP methods the route responds to.
     *
     * @var array
     */
    protected array $methods;

    /**
     * The URI pattern the route responds to.
     *
     * @var string
     */
    protected string $uri;

    /**
     * The action that should be executed when the route is matched.
     * Can be a Closure or an array [ControllerClass::class, 'method'].
     *
     * @var mixed
     */
    protected mixed $action;

    /**
     * Route middleware.
     *
     * @var array
     */
    protected array $middleware = [];

    /**
     * Route name.
     *
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * Extracted route parameters after matching.
     *
     * @var array
     */
    protected array $parameters = [];

    /**
     * Create a new Route instance.
     *
     * @param array|string $methods
     * @param string $uri
     * @param mixed $action
     */
    public function __construct(array|string $methods, string $uri, mixed $action)
    {
        $this->methods = (array) $methods;
        $this->uri = '/' . trim($uri, '/');
        $this->action = $action;
    }

    /**
     * Get the HTTP methods for this route.
     *
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Get the URI for this route.
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Get the action for this route.
     *
     * @return mixed
     */
    public function getAction(): mixed
    {
        return $this->action;
    }

    /**
     * Add middleware to the route.
     *
     * @param string|array $middleware
     * @return self
     */
    public function middleware(string|array $middleware): self
    {
        $middleware = is_array($middleware) ? $middleware : func_get_args();
        $this->middleware = array_merge($this->middleware, $middleware);
        
        return $this;
    }

    /**
     * Get the middleware attached to the route.
     *
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * Set a name for the route.
     *
     * @param string $name
     * @return self
     */
    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the route name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Determine if the route matches a given URI.
     *
     * @param string $requestUri
     * @return bool
     */
    public function matches(string $requestUri): bool
    {
        // Simple exact match
        if ($this->uri === $requestUri) {
            return true;
        }

        // Advanced regex matching for parameters (e.g., /users/{id})
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_-]+)', $this->uri);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $requestUri, $matches)) {
            // Extract named parameters
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $this->parameters[$key] = $value;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Get the extracted parameters.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}