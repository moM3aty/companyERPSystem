<?php
// Path: resources/views/documents/common/totals.php
/*
 * Reusable Totals Section for Financial Documents
 * Expected Variables:
 * $subtotal (Float)
 * $discount (Float - Optional)
 * $vatPercentage (Float)
 * $vatAmount (Float)
 * $grandTotal (Float)
 * $currency (String, default: 'SAR')
 * $amountInWords (String - Optional)
 */
$currency = $currency ?? 'SAR';
?>
<div class="flex justify-between items-start mt-6 page-break-inside-avoid">
    
    <!-- Left Side: Amount in Words or Notes -->
    <div class="w-1/2 bg-gray-50 p-4 rounded-lg border border-gray-200">
        <?php if(!empty($amountInWords)): ?>
            <div class="mb-3">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Amount in Words:</p>
                <p class="text-sm font-medium text-gray-800 italic"><?= htmlspecialchars($amountInWords) ?></p>
            </div>
        <?php else: ?>
            <div class="text-xs text-gray-500">
                <p class="font-bold text-gray-700 mb-1">Standard Terms:</p>
                <p>All prices are in <?= htmlspecialchars($currency) ?> unless otherwise stated.</p>
                <p>Any discrepancies must be reported within 7 days of document date.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Side: Calculations -->
    <div class="w-5/12">
        <table class="w-full text-right text-sm border-collapse">
            <tbody>
                <!-- Subtotal -->
                <tr>
                    <td class="py-2 px-3 text-gray-600 border-b border-gray-100 font-medium">
                        Subtotal (Excl. VAT)
                    </td>
                    <td class="py-2 px-3 font-semibold text-gray-900 border-b border-gray-100 w-32">
                        <?= number_format($subtotal, 2) ?>
                    </td>
                </tr>
                
                <!-- Discount -->
                <?php if(isset($discount) && $discount > 0): ?>
                <tr>
                    <td class="py-2 px-3 text-gray-600 border-b border-gray-100 font-medium">
                        Total Discount
                    </td>
                    <td class="py-2 px-3 font-semibold text-red-600 border-b border-gray-100">
                        -<?= number_format($discount, 2) ?>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- VAT -->
                <tr>
                    <td class="py-2 px-3 text-gray-600 border-b border-gray-300 font-medium">
                        Total VAT (<?= htmlspecialchars($vatPercentage) ?>%)
                    </td>
                    <td class="py-2 px-3 font-semibold text-gray-900 border-b border-gray-300">
                        <?= number_format($vatAmount, 2) ?>
                    </td>
                </tr>

                <!-- Grand Total -->
                <tr class="bg-gray-900 text-white">
                    <td class="py-3 px-3 rounded-l-lg font-bold text-base uppercase tracking-wider">
                        Grand Total Due
                    </td>
                    <td class="py-3 px-3 font-black text-xl rounded-r-lg whitespace-nowrap">
                        <?= number_format($grandTotal, 2) ?> <span class="text-sm font-medium text-gray-300"><?= htmlspecialchars($currency) ?></span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>