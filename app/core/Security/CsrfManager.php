<?php
// Path: app/Core/Security/CsrfManager.php

declare(strict_types=1);

namespace App\Core\Security;

use App\Core\Auth\SessionManager;
use App\Core\Config\SecurityConfig;

/**
 * Enterprise CSRF Manager
 * Protects forms and state-changing requests against Cross-Site Request Forgery.
 */
class CsrfManager
{
    protected SessionManager $session;
    protected SecurityConfig $config;
    
    /**
     * The name of the token key in the session and requests.
     *
     * @var string
     */
    protected string $tokenName;

    /**
     * CsrfManager constructor.
     *
     * @param SessionManager $session
     * @param SecurityConfig $config
     */
    public function __construct(SessionManager $session, SecurityConfig $config)
    {
        $this->session = $session;
        $this->config = $config;
        $this->tokenName = $this->config->csrfTokenName;
    }

    /**
     * Get the current CSRF token from the session. If one doesn't exist, generate it.
     *
     * @return string
     */
    public function getToken(): string
    {
        // Ensure session is active
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION[$this->tokenName])) {
            $this->regenerateToken();
        }

        return $_SESSION[$this->tokenName];
    }

    /**
     * Regenerate the CSRF token securely.
     *
     * @return void
     */
    public function regenerateToken(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        $_SESSION[$this->tokenName] = bin2hex(random_bytes(32));
    }

    /**
     * Verify a given CSRF token against the one stored in the session.
     * Uses hash_equals to prevent timing attacks.
     *
     * @param string|null $token
     * @return bool
     */
    public function verifyToken(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $storedToken = $this->getToken();

        return hash_equals($storedToken, $token);
    }

    /**
     * Generate a hidden HTML input field containing the CSRF token.
     * Useful for embedding in standard web forms.
     *
     * @return string
     */
    public function generateHtmlField(): string
    {
        $token = $this->getToken();
        $name = htmlspecialchars($this->tokenName, ENT_QUOTES, 'UTF-8');
        
        return sprintf('<input type="hidden" name="%s" value="%s">', $name, $token);
    }

    /**
     * Get the configured token name (e.g., '_token').
     *
     * @return string
     */
    public function getTokenName(): string
    {
        return $this->tokenName;
    }
}