<?php
// Path: app/Domain/Contracts/UnitOfWorkInterface.php

declare(strict_types=1);

namespace App\Domain\Contracts;

/**
 * Enterprise Domain Contract: Unit of Work
 * يضمن تطبيق النزاهة (ACID) للعمليات داخل طبقة ה-Domain دون الاعتماد المباشر على (DB Facades).
 */
interface UnitOfWorkInterface
{
    public function beginTransaction(): void;
    
    public function commit(): void;
    
    public function rollBack(): void;
    
    /**
     * تنفيذ دالة داخل إطار معاملة آمنة (Atomic Transaction).
     *
     * @param callable $operation
     * @return mixed
     */
    public function execute(callable $operation): mixed;
}