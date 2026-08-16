<?php
// Path: app/Core/Http/Request.php

declare(strict_types=1);

namespace App\Core\Http;

/**
 * Enterprise HTTP Request
 * نسخة محسنة (Bulletproof) مخصصة للعمل داخل المجلدات الفرعية (Sub-folders) 
 * على الاستضافات المشتركة وتخطي أخطاء الـ SCRIPT_NAME.
 */
class Request
{
    protected array $query;
    protected array $request;
    protected array $server;
    protected array $files;
    protected array $cookies;
    protected array $headers;

    public function __construct(
        array $query = [],
        array $request = [],
        array $server = [],
        array $files = [],
        array $cookies = []
    ) {
        $this->query = $query;
        $this->request = $request;
        $this->server = $server;
        $this->files = $files;
        $this->cookies = $cookies;
        $this->headers = $this->extractHeaders($server);
    }

    public static function capture(): static
    {
        return new static($_GET, $_POST, $_SERVER, $_FILES, $_COOKIE);
    }

    public function method(): string
    {
        return strtoupper((string) ($this->server['REQUEST_METHOD'] ?? 'GET'));
    }

    /**
     * استخراج الـ URI بقوة جبرية لتخطي المجلدات الفرعية.
     */
    public function uri(): string
    {
        $uri = parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        
        // 1. المحاولة الأولى: الاستخراج الديناميكي
        $scriptName = $this->server['SCRIPT_NAME'] ?? '';
        $baseDir = dirname($scriptName);
        
        // 2. المحاولة الثانية: المسار الثابت لحالتك المحددة
        $hardcodedBase = '/ERP/public';

        if ($baseDir !== '/' && $baseDir !== '\\' && str_starts_with($uri, $baseDir)) {
            $uri = substr($uri, strlen($baseDir));
        } elseif (str_starts_with($uri, $hardcodedBase)) {
            $uri = substr($uri, strlen($hardcodedBase));
        }

        return '/' . trim($uri, '/');
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->request[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->request[$key] ?? $this->query[$key] ?? $default;
    }

    public function server(string $key, mixed $default = null): mixed
    {
        return $this->server[$key] ?? $default;
    }

    protected function extractHeaders(array $server): array
    {
        $headers = [];
        foreach ($server as $key => $value) {
            if (str_starts_with((string)$key, 'HTTP_')) {
                $headerName = str_replace('_', '-', strtolower(substr((string)$key, 5)));
                $headers[$headerName] = $value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'])) {
                $headerName = str_replace('_', '-', strtolower((string)$key));
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }

    public function ajax(): bool
    {
        return strtolower((string) ($this->headers['x-requested-with'] ?? '')) === 'xmlhttprequest';
    }
}