<?php
// Path: app/Core/Http/RedirectResponse.php

declare(strict_types=1);

namespace App\Core\Http;

/**
 * Enterprise Redirect Response
 * Handles HTTP redirection securely.
 */
class RedirectResponse extends Response
{
    /**
     * Create a new Redirect Response instance.
     *
     * @param string $url
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct(string $url, int $statusCode = 302, array $headers = [])
    {
        parent::__construct('', $statusCode, $headers);
        
        $this->setHeader('Location', $url);
    }

    /**
     * Set the target URL for the redirection.
     *
     * @param string $url
     * @return self
     */
    public function setTargetUrl(string $url): self
    {
        $this->setHeader('Location', $url);
        
        return $this;
    }
}