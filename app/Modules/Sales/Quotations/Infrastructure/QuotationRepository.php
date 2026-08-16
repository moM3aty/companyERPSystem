<?php
// Path: app/Modules/Sales/Quotations/Infrastructure/QuotationRepository.php

declare(strict_types=1);

namespace App\Modules\Sales\Quotations\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Sales\Quotations\Domain\QuotationRepositoryInterface;

class QuotationRepository extends BaseRepository implements QuotationRepositoryInterface
{
    protected string $table = 'sales_quotations';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function generateQuotationNumber(int $companyId): string
    {
        $prefix = 'QT-' . date('ym') . '-';
        
        $lastRow = $this->newQuery()
            ->select(['quotation_no'])
            ->where('company_id', '=', $companyId)
            ->where('quotation_no', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastRow) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastRow['quotation_no']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function bulkInsertItems(int $quotationId, array $items): void
    {
        if (empty($items)) return;

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?, ?, ?, ?, ?)";

        foreach ($items as $item) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $quotationId,
                $item['product_id'],
                $item['description'] ?? null,
                $item['quantity'],
                $item['unit_price'],
                $item['discount_amount'] ?? 0.00,
                $item['tax_amount'] ?? 0.00,
                $item['total']
            );
        }

        $sql = "INSERT INTO sales_quotation_items 
                (quotation_id, product_id, description, quantity, unit_price, discount_amount, tax_amount, total) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }
}