<?php
// Path: app/Domain/Exceptions/EntityNotFoundException.php

declare(strict_types=1);

namespace App\Domain\Exceptions;

/**
 * Enterprise Domain Exception: Entity Not Found
 * يُرمى داخل طبقة ה-Domain حصراً (ولا يعتمد على أكواد الـ Core/Database).
 */
class EntityNotFoundException extends DomainException
{
    /**
     * EntityNotFoundException constructor.
     *
     * @param string $entity اسم الكيان (مثال: Invoice, Customer)
     * @param mixed $id المعرف
     */
    public function __construct(string $entity, mixed $id)
    {
        parent::__construct("The requested entity of type [{$entity}] with ID [{$id}] was not found in the domain.", 404);
    }
}