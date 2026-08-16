<?php
// Path: app/Core/Http/Middleware/MiddlewarePipeline.php

declare(strict_types=1);

namespace App\Core\Http\Middleware;

use Closure;
use Exception;
use Throwable;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Bootstrap\Container;

/**
 * Enterprise Middleware Pipeline (Onion Architecture)
 * Sends a Request through a series of Middleware layers before hitting the core application logic.
 */
class MiddlewarePipeline
{
    /**
     * The dependency injection container.
     *
     * @var Container
     */
    protected Container $container;

    /**
     * The object being passed through the pipeline (The Request).
     *
     * @var Request
     */
    protected Request $request;

    /**
     * The array of class pipes (Middlewares).
     *
     * @var array
     */
    protected array $middlewares = [];

    /**
     * Create a new Middleware Pipeline instance.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Set the object being sent through the pipeline.
     *
     * @param Request $request
     * @return self
     */
    public function send(Request $request): self
    {
        $this->request = $request;
        
        return $this;
    }

    /**
     * Set the array of middlewares.
     *
     * @param array $middlewares
     * @return self
     */
    public function through(array $middlewares): self
    {
        $this->middlewares = $middlewares;
        
        return $this;
    }

    /**
     * Run the pipeline with a final destination callback.
     *
     * @param Closure $destination
     * @return Response
     * @throws Throwable
     */
    public function then(Closure $destination): Response
    {
        // We reverse the array so the outer layer of the onion is built first.
        // array_reduce wraps each middleware around the next one recursively.
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            $this->getSlice(),
            $this->prepareDestination($destination)
        );

        return $pipeline($this->request);
    }

    /**
     * Get a Closure that represents a slice of the application onion.
     *
     * @return Closure
     */
    protected function getSlice(): Closure
    {
        return function (Closure $nextLayer, string|MiddlewareInterface $middleware) {
            return function (Request $request) use ($nextLayer, $middleware) {
                
                // If the middleware is a string class name, resolve it from the DI Container
                if (is_string($middleware)) {
                    $middleware = $this->container->make($middleware);
                }

                // Strict Type checking to ensure Enterprise standards
                if (!$middleware instanceof MiddlewareInterface) {
                    throw new Exception("Middleware [" . get_class($middleware) . "] must implement MiddlewareInterface.");
                }

                // Execute the middleware and pass the next layer to it
                return $middleware->process($request, $nextLayer);
            };
        };
    }

    /**
     * Get the final piece of the Closure onion (usually the Controller action).
     *
     * @param Closure $destination
     * @return Closure
     */
    protected function prepareDestination(Closure $destination): Closure
    {
        return function (Request $request) use ($destination) {
            try {
                return $destination($request);
            } catch (Throwable $e) {
                // Let the Kernel catch this and format it properly
                throw $e;
            }
        };
    }
}