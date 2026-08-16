<?php
// Path: app/Core/Config/SecurityConfig.php

declare(strict_types=1);

namespace App\Core\Config;

/**
 * Enterprise Security Configuration
 * Manages encryption keys, CSRF tokens, and session lifecycles.
 */
class SecurityConfig
{
    public readonly string $csrfTokenName;
    public readonly int $sessionLifetime;
    public readonly string $encryptionKey;

    /**
     * SecurityConfig constructor.
     *
     * @param Config $config The central configuration manager.
     */
    public function __construct(Config $config)
    {
        $this->csrfTokenName = $config->get('security.csrf_token_name', '_token');
        $this->sessionLifetime = (int) $config->get('security.session_lifetime', 7200);
        $this->encryptionKey = $config->get('security.encryption_key', '');
    }

    /**
     * Get the default security configurations.
     *
     * @return array
     */
    public static function getDefaults(): array
    {
        return [
            'csrf_token_name' => '_token',
            'session_lifetime' => 7200, // 2 hours
            'encryption_key' => 'CHANGE_THIS_TO_A_LONG_RANDOM_SECRET_KEY_2026',
            
            // These map to your requested ini_set directives for session security
            'session_cookie_path' => '/',
            'session_cookie_httponly' => true,
            'session_use_strict_mode' => true,
            'session_cookie_samesite' => 'Lax',
            'session_cookie_secure' => true,
        ];
    }
}