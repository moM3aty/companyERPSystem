<?php
// Path: app/Core/Numbering/NumberingContext.php

declare(strict_types=1);

namespace App\Core\Numbering;

/**
 * Enterprise Numbering Context
 * يحمل البيانات المتعلقة باللحظة التي يتم فيها طلب الترقيم (كالتاريخ الحالي والشركة).
 */
class NumberingContext
{
    public readonly ?int $companyId;
    public readonly ?int $branchId;
    public readonly ?string $documentDate;

    /**
     * NumberingContext constructor.
     *
     * @param int|null $companyId
     * @param int|null $branchId
     * @param string|null $documentDate صيغة التاريخ (Y-m-d)
     */
    public function __construct(
        ?int $companyId = null,
        ?int $branchId = null,
        ?string $documentDate = null
    ) {
        $this->companyId = $companyId;
        $this->branchId = $branchId;
        $this->documentDate = $documentDate ?: date('Y-m-d');
    }
}