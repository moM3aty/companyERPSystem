<?php
// Path: app/Core/Auth/AuthManager.php

declare(strict_types=1);

namespace App\Core\Auth;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\AuthenticationException;

/**
 * Enterprise Authentication Manager
 * Orchestrates login logic, password verification, and updates database records (e.g., last_login_at).
 */
class AuthManager
{
    protected DatabaseManager $db;
    protected SessionManager $session;
    protected TokenManager $tokenManager;

    /**
     * AuthManager constructor.
     *
     * @param DatabaseManager $db
     * @param SessionManager $session
     * @param TokenManager $tokenManager
     */
    public function __construct(DatabaseManager $db, SessionManager $session, TokenManager $tokenManager)
    {
        $this->db = $db;
        $this->session = $session;
        $this->tokenManager = $tokenManager;
    }

    /**
     * Attempt to authenticate a user using email and password.
     *
     * @param string $email
     * @param string $password
     * @param bool $issueToken If true, returns a JWT instead of using sessions.
     * @return AuthUser|string|false Returns AuthUser on session success, Token string on API success, false on failure.
     * @throws AuthenticationException
     */
    public function authenticate(string $email, string $password, bool $issueToken = false): AuthUser|string|false
    {
        $query = "SELECT id, company_id, username, email, password_hash, employee_id, language, timezone, is_active 
                  FROM users 
                  WHERE email = ? AND deleted_at IS NULL LIMIT 1";

        $userData = $this->db->connection()->selectOne($query, [$email]);

        // 1. Verify User Exists
        if (!$userData) {
            return false; // User not found
        }

        // 2. Verify Account Status
        if ((int) $userData['is_active'] !== 1) {
            throw new AuthenticationException('This account is disabled. Please contact the administrator.', 403);
        }

        // 3. Verify Password using secure PHP native password_verify
        if (!password_verify($password, $userData['password_hash'])) {
            return false; // Invalid password
        }

        // 4. Create the AuthUser Object
        $user = new AuthUser(
            (int) $userData['id'],
            (int) $userData['company_id'],
            (string) $userData['username'],
            (string) $userData['email'],
            $userData['employee_id'] ? (int) $userData['employee_id'] : null,
            (string) ($userData['language'] ?? 'ar'),
            (string) ($userData['timezone'] ?? 'Asia/Riyadh')
        );

        // 5. Update last login timestamp safely
        $updateQuery = "UPDATE users SET last_login_at = ? WHERE id = ?";
        $this->db->connection()->update($updateQuery, [date('Y-m-d H:i:s'), $user->id]);

        // 6. Return context based on platform (Web vs API)
        if ($issueToken) {
            return $this->tokenManager->generateToken($user);
        }

        $this->session->login($user);
        return $user;
    }

    /**
     * Log the current user out.
     *
     * @return void
     */
    public function logout(): void
    {
        $this->session->logout();
    }

    /**
     * Get the currently authenticated user from the session.
     *
     * @return AuthUser|null
     */
    public function user(): ?AuthUser
    {
        return $this->session->getUser();
    }

    /**
     * Check if a user is authenticated.
     *
     * @return bool
     */
    public function check(): bool
    {
        return $this->session->check();
    }
}