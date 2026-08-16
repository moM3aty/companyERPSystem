<?php
// Path: app/Core/Security/HashManager.php

declare(strict_types=1);

namespace App\Core\Security;

use RuntimeException;

/**
 * Enterprise Hash Manager
 * Provides highly secure, irreversible password hashing using PHP's native algorithms.
 * Automatically prefers Argon2id if available, falling back to Bcrypt.
 */
class HashManager
{
    /**
     * The hashing algorithm to use.
     *
     * @var string
     */
    protected string $algorithm;

    /**
     * HashManager constructor.
     */
    public function __construct()
    {
        // Fallback to PASSWORD_BCRYPT if Argon2id is not supported by the current PHP environment
        $this->algorithm = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
    }

    /**
     * Hash a plain-text value (e.g., a password).
     *
     * @param string $value
     * @param array $options
     * @return string
     * @throws RuntimeException
     */
    public function make(string $value, array $options = []): string
    {
        $hashOptions = $this->getOptions($options);
        
        $hash = password_hash($value, $this->algorithm, $hashOptions);

        if ($hash === false) {
            throw new RuntimeException('Bcrypt/Argon2 hashing failed. Please check PHP configuration.');
        }

        return $hash;
    }

    /**
     * Verify that a plain-text value matches a given hash.
     *
     * @param string $value
     * @param string $hashedValue
     * @return bool
     */
    public function check(string $value, string $hashedValue): bool
    {
        if (strlen($hashedValue) === 0) {
            return false;
        }

        return password_verify($value, $hashedValue);
    }

    /**
     * Check if a given hash needs to be re-hashed based on current algorithms/work factors.
     * Useful for automatically upgrading password security when users log in.
     *
     * @param string $hashedValue
     * @param array $options
     * @return bool
     */
    public function needsRehash(string $hashedValue, array $options = []): bool
    {
        return password_needs_rehash($hashedValue, $this->algorithm, $this->getOptions($options));
    }

    /**
     * Get the options for the hashing algorithm.
     * Provides enterprise-grade defaults for Bcrypt cost and Argon2 memory/time factors.
     *
     * @param array $options
     * @return array
     */
    protected function getOptions(array $options): array
    {
        if ($this->algorithm === PASSWORD_BCRYPT) {
            return [
                'cost' => $options['cost'] ?? 12, // Standard secure cost for Bcrypt
            ];
        }

        if (defined('PASSWORD_ARGON2ID') && $this->algorithm === PASSWORD_ARGON2ID) {
            return [
                'memory_cost' => $options['memory_cost'] ?? 65536, // 64 MB
                'time_cost'   => $options['time_cost'] ?? 4,
                'threads'     => $options['threads'] ?? 2,
            ];
        }

        return [];
    }
}