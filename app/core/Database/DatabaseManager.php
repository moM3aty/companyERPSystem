<?php
// Path: app/Core/Database/DatabaseManager.php

declare(strict_types=1);

namespace App\Core\Database;

use PDO;
use PDOException;
use App\Core\Config\Config;
use App\Core\Config\DatabaseConfig;
use App\Core\Exceptions\DatabaseException;

/**
 * Enterprise Database Manager
 * Acts as a factory and registry for database connections (Connection Pool).
 */
class DatabaseManager
{
    /**
     * The application configuration repository.
     *
     * @var Config
     */
    protected Config $config;

    /**
     * The active connection instances.
     *
     * @var array<string, Connection>
     */
    protected array $connections = [];

    /**
     * The custom connection resolvers.
     *
     * @var array<string, callable>
     */
    protected array $extensions = [];

    /**
     * DatabaseManager constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get a database connection instance.
     *
     * @param string|null $name
     * @return Connection
     */
    public function connection(?string $name = null): Connection
    {
        $name = $name ?: $this->getDefaultConnection();

        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->makeConnection($name);
        }

        return $this->connections[$name];
    }

    /**
     * Make the database connection instance.
     *
     * @param string $name
     * @return Connection
     * @throws DatabaseException
     */
    protected function makeConnection(string $name): Connection
    {
        // Check if there's a custom extension/resolver for this connection name
        if (isset($this->extensions[$name])) {
            return call_user_func($this->extensions[$name], $this->config, $name);
        }

        // Get configuration. If config is missing, fallback to defaults (from AppConfig mapped earlier).
        $config = $this->configuration($name);

        $pdo = $this->createPdoConnection($config);

        return new Connection($pdo);
    }

    /**
     * Get the configuration for a connection.
     *
     * @param string $name
     * @return array
     * @throws DatabaseException
     */
    protected function configuration(string $name): array
    {
        $connections = $this->config->get('database.connections', []);

        if (empty($connections) && $name === 'default') {
            // Fallback to our securely defined defaults if the multidimensional array is not set yet
            return DatabaseConfig::getDefaults();
        }

        if (!isset($connections[$name])) {
            throw new DatabaseException("Database connection [{$name}] not configured.");
        }

        return $connections[$name];
    }

    /**
     * Create a new PDO connection securely.
     *
     * @param array $config
     * @return PDO
     * @throws DatabaseException
     */
    protected function createPdoConnection(array $config): PDO
    {
        $host = $config['host'] ?? '127.0.0.1';
        $db = $config['name'] ?? $config['database'] ?? '';
        $port = $config['port'] ?? '3306';
        $charset = $config['charset'] ?? 'utf8mb4';
        
        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";
        
        $username = $config['username'] ?? 'root';
        $password = $config['password'] ?? '';
        
        // Strict, secure default PDO options matching Enterprise standards
        $options = $config['options'] ?? [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ];

        try {
            return new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new DatabaseException("Could not connect to the database. " . $e->getMessage(), [], $e);
        }
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection(): string
    {
        return $this->config->get('database.default', 'default');
    }

    /**
     * Set the default connection name.
     *
     * @param string $name
     * @return void
     */
    public function setDefaultConnection(string $name): void
    {
        $this->config->set('database.default', $name);
    }

    /**
     * Register an extension connection resolver.
     * Helpful for Multi-Tenant scenarios where a connection needs specific dynamic logic.
     *
     * @param string $name
     * @param callable $resolver
     * @return void
     */
    public function extend(string $name, callable $resolver): void
    {
        $this->extensions[$name] = $resolver;
    }
    
    /**
     * Dynamically pass methods to the default connection.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->connection()->$method(...$parameters);
    }
}