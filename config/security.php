<?php
// Path: config/security.php

declare(strict_types=1);


/**
 * Enterprise Security & Auth Configuration
 * Configures encryption keys, session security, and multi-factor auth parameters.
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Encryption Key & Token Names
    |--------------------------------------------------------------------------
    */
    'encryption_key' => env('APP_KEY', 'nour_trust_erp_secret_key_32bytes_min!'),

    'csrf_token_name' => '_erp_token',

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime & Cookie Security
    |--------------------------------------------------------------------------
    */
    'session_lifetime' => 7200, // 2 hours in seconds

    'session_cookie_path' => '/',

    'session_cookie_httponly' => true,

    'session_use_strict_mode' => true,

    'session_cookie_samesite' => 'Lax',

    'session_cookie_secure' => env('SESSION_SECURE_COOKIE', true),

    /*
    |--------------------------------------------------------------------------
    | Multi-Factor Authentication
    |--------------------------------------------------------------------------
    */
    'mfa_enabled_globally' => false,
];