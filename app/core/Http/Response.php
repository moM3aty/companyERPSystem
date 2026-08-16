<?php
// Path: app/Core/Http/Response.php

declare(strict_types=1);

namespace App\Core\Http;

/**
 * Enterprise HTTP Response
 * Represents an outgoing HTTP response with content, status code, and headers.
 */
class Response
{
    /**
     * The response content.
     *
     * @var mixed
     */
    protected mixed $content;

    /**
     * The HTTP status code.
     *
     * @var int
     */
    protected int $statusCode;

    /**
     * The response headers.
     *
     * @var array
     */
    protected array $headers;

    /**
     * Create a new Response instance.
     *
     * @param mixed $content
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct(mixed $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Set a header on the Response.
     *
     * @param string $key
     * @param string $value
     * @return self
     */
    public function setHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        
        return $this;
    }

    /**
     * Set the HTTP status code.
     *
     * @param int $code
     * @return self
     */
    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        
        return $this;
    }

    /**
     * Get the HTTP status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get the response content.
     *
     * @return mixed
     */
    public function getContent(): mixed
    {
        return $this->content;
    }

    /**
     * Send the HTTP headers and content to the client.
     *
     * @return void
     */
    public function send(): void
    {
        $this->sendHeaders();
        $this->sendContent();
    }

    /**
     * Send HTTP headers.
     *
     * @return void
     */
    protected function sendHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}", true, $this->statusCode);
        }
    }

    /**
     * Send content.
     *
     * @return void
     */
    protected function sendContent(): void
    {
        echo $this->content;
    }
}