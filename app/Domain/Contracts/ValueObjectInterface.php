<?php
// Path: app/Domain/Contracts/ValueObjectInterface.php

declare(strict_types=1);

namespace App\Domain\Contracts;

/**
 * Enterprise Contract: Value Object
 * يلزم الكائنات القيمية بأن تكون غير قابلة للتغيير (Immutable) وأن توفر دالة للمقارنة.
 */
interface ValueObjectInterface
{
    /**
     * التحقق مما إذا كان كائنان يحملان نفس القيمة تماماً.
     *
     * @param self $other
     * @return bool
     */
    public function equals(self $other): bool;
}