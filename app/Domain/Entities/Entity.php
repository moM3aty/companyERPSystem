<?php
// Path: app/Domain/Entities/Entity.php

declare(strict_types=1);

namespace App\Domain\Entities;

/**
 * Enterprise Domain Base: Entity
 * الكيان المجرد لطبقة الـ Domain (يختلف عن الـ Core/Models/Entity الذي يتعامل مع ה-DTO والـ Casts).
 * هنا نركز على الهوية (Identity) والمساواة (Equality) فقط.
 */
abstract class Entity
{
    protected mixed $id;

    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * كيانان يتساويان فقط إذا كان لهما نفس المُعرّف ونفس النوع (Class).
     *
     * @param self $other
     * @return bool
     */
    public function equals(self $other): bool
    {
        if (get_class($this) !== get_class($other)) {
            return false;
        }

        return $this->id !== null && $this->id === $other->getId();
    }
}