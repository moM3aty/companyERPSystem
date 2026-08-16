<?php
// Path: app/Core/Auth/SessionManager.php

declare(strict_types=1);

namespace App\Core\Auth;

/**
 * Enterprise Web Session Manager
 * Handles secure session authentication for traditional web interfaces.
 */
class SessionManager
{
    protected const SESSION_KEY = 'auth_user';

    /**
     * Log a user into the session securely.
     * Prevents Session Fixation attacks by regenerating the session ID.
     *
     * @param AuthUser $user
     * @return void
     */
    public function login(AuthUser $user): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Regenerate ID to prevent session fixation vulnerability
        session_regenerate_id(true);

        $_SESSION[self::SESSION_KEY] = $user->toArray();
    }

    /**
     * Log the current user out and destroy the session data.
     *
     * @return void
     */
    public function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Unset the specific auth key
        unset($_SESSION[self::SESSION_KEY]);
        
        // Optionally destroy the entire session if it's purely an auth session
        // session_destroy(); 
    }

    /**
     * Check if a user is currently authenticated in the session.
     *
     * @return bool
     */
    public function check(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]) && is_array($_SESSION[self::SESSION_KEY]);
    }

    /**
     * Get the currently authenticated user from the session.
     *
     * @return AuthUser|null
     */
    public function getUser(): ?AuthUser
    {
        if (!$this->check()) {
            return null;
        }

        $data = $_SESSION[self::SESSION_KEY];

        return new AuthUser(
            (int) $data['id'],
            (int) $data['company_id'],
            (string) $data['username'],
            (string) $data['email'],
            $data['employee_id'] ? (int) $data['employee_id'] : null,
            (string) ($data['language'] ?? 'ar'),
            (string) ($data['timezone'] ?? 'Asia/Riyadh')
        );
    }
}