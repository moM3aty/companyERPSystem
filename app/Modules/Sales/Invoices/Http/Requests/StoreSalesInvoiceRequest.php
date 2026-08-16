<?php
// Path: app/Modules/Sales/Invoices/Http/Requests/StoreSalesInvoiceRequest.php

declare(strict_types=1);

namespace App\Modules\Sales\Invoices\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

/**
 * Enterprise Request Validation: Store Sales Invoice
 * يضمن صحة هيكل الفاتورة والأصناف بداخلها قبل تمريرها لمرحلة العمليات الحسابية.
 */
class StoreSalesInvoiceRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * التحقق الشامل من الترويسة وسطور الفاتورة.
     *
     * @param array $data
     * @param int $companyId
     * @return array
     * @throws ValidationException
     */
    public function validate(array $data, int $companyId): array
    {
        // 1. التحقق من ترويسة الفاتورة
        $rules = [
            'customer_id'  => [new Required(), new Exists($this->db, 'customers', 'id', $companyId)],
            'invoice_date' => [new Required(), new Date('Y-m-d')],
            'due_date'     => [new Required(), new Date('Y-m-d')],
            'currency_id'  => [new Required(), new Exists($this->db, 'currencies', 'id')],
            'items'        => [new Required()], 
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        // 2. التحقق المخصص من الأصناف داخل الفاتورة
        if (!is_array($data['items']) || count($data['items']) === 0) {
            throw new ValidationException(['items' => ['The invoice must contain at least one item.']]);
        }

        foreach ($data['items'] as $index => $item) {
            $itemRules = [
                'product_id'      => [new Required(), new Exists($this->db, 'products', 'id', $companyId)],
                'quantity'        => [new Required(), new Numeric(), new Min(0.01)],
                'unit_price'      => [new Required(), new Numeric(), new Min(0)],
                'discount_amount' => [new Numeric(), new Min(0)],
                'tax_amount'      => [new Numeric(), new Min(0)],
            ];
            
            try {
                $validatedItem = ValidatorFactory::makeAndValidate($item, $itemRules);
                $validated['items'][$index] = $validatedItem;
                
                // الاحتفاظ بالوصف والمستودع إن وُجد
                $validated['items'][$index]['description'] = $item['description'] ?? null;
                $validated['items'][$index]['warehouse_id'] = $item['warehouse_id'] ?? null;
                
            } catch (ValidationException $e) {
                // إرجاع رسالة خطأ توضح أي سطر تحديداً فشل
                throw new ValidationException(["items.{$index}" => $e->getErrors()], "Validation failed for invoice item at row " . ($index + 1));
            }
        }

        return $validated;
    }
}