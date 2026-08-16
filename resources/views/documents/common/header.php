<?php
// Path: resources/views/documents/common/header.php
/*
 * Reusable Header for all printed documents (Invoices, POs, Receipts, etc.)
 * Expected Variables: 
 * $docTitle (e.g., 'Tax Invoice', 'Purchase Order')
 * $docTitleAr (e.g., 'فاتورة ضريبية', 'أمر شراء')
 * $docNumber (e.g., 'INV-2026-089')
 * $companyInfo (Array with company details)
 * $metaData (Array of key-value pairs for document info like Date, Due Date, etc.)
 * $qrCodeUrl (Optional: URL or Base64 for ZATCA QR Code)
 */

// Define defaults to prevent "Undefined variable" IDE warnings
$docTitle = $docTitle ?? 'Document';
$docTitleAr = $docTitleAr ?? '';
$docNumber = $docNumber ?? 'N/A';
$companyInfo = $companyInfo ?? [];
$metaData = $metaData ?? [];
$qrCodeUrl = $qrCodeUrl ?? null;
?>
<div class="flex justify-between items-start mb-6 border-b-2 border-gray-800 pb-4">
    
    <!-- Company Details -->
    <div class="flex gap-4 items-center w-1/3">
        <div class="w-20 h-20 rounded-xl bg-gradient-to-tr from-gray-900 to-blue-700 flex items-center justify-center text-white font-black text-4xl shadow-md print:border print:border-gray-300">
            <?= htmlspecialchars($companyInfo['logo_text'] ?? 'NT') ?>
        </div>
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight"><?= htmlspecialchars($companyInfo['name_en'] ?? 'Nour Trust Enterprise') ?></h1>
            <p class="text-gray-600 text-xs mt-1 font-medium font-['Cairo']"><?= htmlspecialchars($companyInfo['name_ar'] ?? 'مؤسسة نور ترست التجارية') ?></p>
            <p class="text-gray-500 text-xs mt-1"><?= htmlspecialchars($companyInfo['address'] ?? 'Riyadh 12345, Saudi Arabia') ?></p>
            <div class="mt-2 text-xs font-semibold text-gray-700 bg-gray-50 p-1.5 rounded border border-gray-100 inline-block">
                VAT No: <?= htmlspecialchars($companyInfo['vat_number'] ?? '300123456789003') ?>
            </div>
        </div>
    </div>

    <!-- Document Title -->
    <div class="text-center w-1/3 pt-2">
        <h2 class="text-2xl font-black uppercase text-blue-700 tracking-wider"><?= htmlspecialchars($docTitle) ?></h2>
        <?php if(!empty($docTitleAr)): ?>
            <h3 class="text-xl font-bold text-gray-700 mt-1 font-['Cairo']"><?= htmlspecialchars($docTitleAr) ?></h3>
        <?php endif; ?>
        <div class="mt-3 inline-block bg-gray-100 px-4 py-1 rounded-full border border-gray-200">
            <span class="font-bold text-gray-900 text-lg">#<?= htmlspecialchars($docNumber) ?></span>
        </div>
    </div>

    <!-- Meta Data & Optional QR -->
    <div class="w-1/3 flex justify-end">
        <?php if(!empty($qrCodeUrl)): ?>
            <!-- ZATCA Phase 2 QR Code -->
            <div class="w-28 h-28 border border-gray-300 p-1 rounded-lg bg-white flex flex-col items-center justify-center mr-4">
                <img src="<?= htmlspecialchars($qrCodeUrl) ?>" alt="QR Code" class="max-w-full mix-blend-multiply">
            </div>
        <?php else: ?>
            <!-- Standard Document Meta -->
            <table class="text-xs text-right border-collapse">
                <tbody>
                    <?php foreach($metaData as $key => $value): ?>
                    <tr>
                        <td class="py-1 px-2 text-gray-500 font-medium uppercase tracking-wider"><?= htmlspecialchars($key) ?>:</td>
                        <td class="py-1 px-2 font-bold text-gray-900"><?= htmlspecialchars($value) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>