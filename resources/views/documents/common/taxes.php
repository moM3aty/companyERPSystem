<?php
// Path: resources/views/documents/common/taxes.php
/*
 * Reusable Tax Breakdown Block for Print Documents (ZATCA Requirement)
 * Expected Variables: $taxBreakdown (Array of tax rates and amounts)
 */
?>
<div class="mt-4 mb-8 page-break-inside-avoid">
    <h4 class="font-bold text-gray-800 uppercase tracking-wider mb-2 text-xs border-b border-gray-200 pb-1">
        Tax Breakdown (تفصيل الضريبة)
    </h4>
    <table class="w-1/2 text-left text-xs border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-100 text-gray-700">
                <th class="py-2 px-3 border-r border-gray-300 font-semibold">Tax Category</th>
                <th class="py-2 px-3 border-r border-gray-300 font-semibold text-right">Taxable Amount</th>
                <th class="py-2 px-3 font-semibold text-right">Tax Amount</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <tr>
                <td class="py-2 px-3 border-r border-gray-200">Standard Rate (15%)</td>
                <td class="py-2 px-3 border-r border-gray-200 text-right font-medium"><?= number_format($subtotal ?? 0, 2) ?></td>
                <td class="py-2 px-3 text-right font-bold text-gray-900"><?= number_format($vatAmount ?? 0, 2) ?></td>
            </tr>
            <!-- Optional Zero Rate Row for completeness -->
            <tr class="text-gray-500">
                <td class="py-2 px-3 border-r border-gray-200">Zero Rate (0%)</td>
                <td class="py-2 px-3 border-r border-gray-200 text-right">0.00</td>
                <td class="py-2 px-3 text-right">0.00</td>
            </tr>
        </tbody>
    </table>
</div>