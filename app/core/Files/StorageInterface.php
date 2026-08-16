<?php
// Path: app/Core/Files/StorageInterface.php

declare(strict_types=1);

namespace App\Core\Files;

/**
 * Enterprise Storage Interface
 * العقد الداخلي الخاص بموديول الملفات لتوحيد التعامل مع أنظمة التخزين المختلفة.
 */
interface StorageInterface
{
    public function put(string $path, string $contents): bool;
    
    public function get(string $path): ?string;
    
    public function exists(string $path): bool;
    
    public function delete(string|array $paths): bool;
    
    public function url(string $path): string;
    
    public function size(string $path): int;
}