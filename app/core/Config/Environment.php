<?php
// Path: app/Core/Config/Environment.php

declare(strict_types=1);

namespace App\Core\Config;

/**
 * Enterprise Environment Bootstrapper
 * Handles PHP Ini directives, Error Reporting, and strict Session Startup logic safely.
 */
class Environment
{
    /**
     * Boot the environment settings (Error reporting & Sessions).
     * 
     * @param Config $config The application configuration repository.
     * @return void
     */
    public static function boot(Config $config): void
    {
        self::configureErrorReporting($config);
        self::bootSession($config);
    }

    /**
     * Configure PHP error reporting based on the current environment.
     *
     * @param Config $config
     * @return void
     */
    private static function configureErrorReporting(Config $config): void
    {
        $env = $config->get('app.env', 'production');

        if ($env === 'development') {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(0);
            ini_set('display_errors', '0');
            ini_set('log_errors', '1');
        }
    }

    /**
     * Initialize the session securely based on Enterprise standards.
     *
     * @param Config $config
     * @return void
     */
    private static function bootSession(Config $config): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            
            // Retrieve security configurations
            $lifetime = (int) $config->get('security.session_lifetime', 7200);
            $appRoot = $config->get('app.root', dirname(__DIR__, 3));

            // Set session GC max lifetime
            ini_set('session.gc_maxlifetime', (string) $lifetime);
            
            // Set session cookies to work across the whole site
            ini_set('session.cookie_path', (string) $config->get('security.session_cookie_path', '/'));
            
            // Protect cookie from XSS (HttpOnly)
            ini_set('session.cookie_httponly', $config->get('security.session_cookie_httponly', true) ? '1' : '0');
            
            // Prevent Session Fixation attacks
            ini_set('session.use_strict_mode', $config->get('security.session_use_strict_mode', true) ? '1' : '0');
            
            // SameSite attribute suitable for site navigation
            ini_set('session.cookie_samesite', (string) $config->get('security.session_cookie_samesite', 'Lax'));
            
            // Require HTTPS for session cookies
            ini_set('session.cookie_secure', $config->get('security.session_cookie_secure', true) ? '1' : '0');

            // Set isolated session directory
            $sessionDir = $appRoot . DIRECTORY_SEPARATOR . 'sessions';

            if (!is_dir($sessionDir)) {
                mkdir($sessionDir, 0755, true);
            }

            if (is_dir($sessionDir) && is_writable($sessionDir)) {
                session_save_path($sessionDir);
            }

            // Start the session safely
            session_start();
        }
    }
}