<?php
// Path: resources/views/documents/common/items-table.php
/*
 * Reusable Items Table Block for Print Documents (Invoices, POs, Quotations)
 * Expected Variables: 
 * $items (Array of item data)
 * $showTax (Boolean) - Whether to show tax column
 * $showDiscount (Boolean) - Whether to show discount column
 */
$showTax = $showTax ?? true;
$showDiscount = $showDiscount ?? true;
?>
<div class="mb-8 border border-gray-300 rounded-lg overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-100 text-gray-700 text-xs uppercase tracking-wider">
                <th class="py-3 px-3 w-10 text-center border-r border-gray-300">#</th>
                <th class="py-3 px-4 font-bold border-r border-gray-300">Item / Description</th>
                <th class="py-3 px-3 w-20 text-center font-bold border-r border-gray-300">Qty</th>
                <th class="py-3 px-3 w-28 text-right font-bold border-r border-gray-300">Unit Price</th>
                <?php if($showDiscount): ?>
                    <th class="py-3 px-3 w-24 text-right font-bold border-r border-gray-300">Disc.</th>
                <?php endif; ?>
                <?php if($showTax): ?>
                    <th class="py-3 px-3 w-24 text-right font-bold border-r border-gray-300">Tax</th>
                <?php endif; ?>
                <th class="py-3 px-4 w-32 text-right font-bold">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 text-sm">
            <?php if(!empty($items)): foreach($items as $index => $item): ?>
            <tr class="<?= $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50' ?>">
                <td class="py-3 px-3 text-center text-gray-500 font-mono border-r border-gray-200"><?= $index + 1 ?></td>
                <td class="py-3 px-4 border-r border-gray-200">
                    <p class="font-bold text-gray-900"><?= htmlspecialchars($item['name'] ?? 'Item Name') ?></p>
                    <?php if(!empty($item['description'])): ?>
                        <p class="text-xs text-gray-500 mt-0.5"><?= htmlspecialchars($item['description']) ?></p>
                    <?php endif; ?>
                </td>
                <td class="py-3 px-3 text-center font-medium border-r border-gray-200"><?= number_format((float)($item['quantity'] ?? 1), 2) ?></td>
                <td class="py-3 px-3 text-right text-gray-700 border-r border-gray-200"><?= number_format((float)($item['unit_price'] ?? 0), 2) ?></td>
                
                <?php if($showDiscount): ?>
                    <td class="py-3 px-3 text-right text-gray-600 border-r border-gray-200"><?= number_format((float)($item['discount_amount'] ?? 0), 2) ?></td>
                <?php endif; ?>
                
                <?php if($showTax): ?>
                    <td class="py-3 px-3 text-right text-gray-600 border-r border-gray-200"><?= number_format((float)($item['tax_amount'] ?? 0), 2) ?></td>
                <?php endif; ?>
                
                <td class="py-3 px-4 text-right font-bold text-gray-900"><?= number_format((float)($item['total'] ?? 0), 2) ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr>
                <td colspan="7" class="py-6 text-center text-gray-400 italic">No items found in this document.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>