<?php
// Path: resources/views/accounting/journal-entries/create.php

$appBaseUrl = rtrim(
    dirname($_SERVER['SCRIPT_NAME'] ?? '/ERP/public/index.php'),
    '/\\'
);

if ($appBaseUrl === '.' || $appBaseUrl === DIRECTORY_SEPARATOR) {
    $appBaseUrl = '';
}

$appUrl = static function (string $path = '') use ($appBaseUrl): string {
    $path = trim($path, '/');

    if ($path === '') {
        return $appBaseUrl !== '' ? $appBaseUrl . '/' : '/';
    }

    return $appBaseUrl . '/' . $path;
};

$appUrlEscaped = static function (string $path = '') use ($appUrl): string {
    return htmlspecialchars($appUrl($path), ENT_QUOTES, 'UTF-8');
};
$pageTitle = 'Create Journal Entry';
ob_start();
?>

<div class="mb-6">
    <a href="<?= $appUrlEscaped('accounting/journal-entries') ?>" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Entries</a>
    <h1 class="text-2xl font-bold text-gray-800">New Manual Journal Entry</h1>
</div>

<form action="<?= $appUrlEscaped('api/v1/accounting/journal-entries') ?>" method="POST">
    <div class="card p-5 mb-6">
        <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Entry Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Entry Date <span class="text-red-500">*</span></label>
                <input type="date" name="entry_date" value="<?= date('Y-m-d') ?>" required class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm bg-gray-50">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference No.</label>
                <input type="text" name="reference" placeholder="e.g. ADJ-0826" class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                <select name="currency" class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm bg-gray-50">
                    <option value="SAR">SAR - Saudi Riyal (Base)</option>
                    <option value="USD">USD - US Dollar</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Attachments</label>
                <input type="file" class="w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-nour-primary hover:file:bg-blue-100 cursor-pointer border border-gray-300 rounded-md py-1 px-2">
            </div>
            <div class="md:col-span-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Memo / Description</label>
                <textarea name="memo" rows="2" class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm" placeholder="Purpose of this entry..."></textarea>
            </div>
        </div>
    </div>

    <div class="card p-0 mb-6">
        <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="text-base font-bold text-gray-800">Ledger Lines</h3>
            <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded font-bold" id="balance-badge">Out of Balance</span>
        </div>
        <div class="p-4">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="text-gray-500 border-b border-gray-200">
                        <th class="pb-2 font-semibold w-1/3">Account</th>
                        <th class="pb-2 font-semibold w-1/3">Description</th>
                        <th class="pb-2 font-semibold w-32 text-right">Debit (DR)</th>
                        <th class="pb-2 font-semibold w-32 text-right">Credit (CR)</th>
                        <th class="pb-2 font-semibold w-10 text-center"></th>
                    </tr>
                </thead>
                <tbody id="lines-container">
                    <!-- Line 1 -->
                    <tr class="border-b border-gray-100">
                        <td class="py-3 pr-2">
                            <select class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5 font-mono">
                                <option value="">Select Account...</option>
                                <option value="6000" selected>6000 - Office Supplies Expense</option>
                            </select>
                        </td>
                        <td class="py-3 pr-2">
                            <input type="text" placeholder="Line description..." class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5">
                        </td>
                        <td class="py-3 pr-2">
                            <input type="number" step="0.01" value="4500.00" class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5 text-right font-medium text-gray-900 debit-input">
                        </td>
                        <td class="py-3 pr-2">
                            <input type="number" step="0.01" placeholder="0.00" class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5 text-right bg-gray-50 credit-input">
                        </td>
                        <td class="py-3 text-center">
                            <button type="button" class="text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <!-- Line 2 -->
                    <tr class="border-b border-gray-100 bg-gray-50/30">
                        <td class="py-3 pr-2">
                            <select class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5 font-mono">
                                <option value="">Select Account...</option>
                                <option value="2100" selected>2100 - Accrued Expenses Payable</option>
                            </select>
                        </td>
                        <td class="py-3 pr-2">
                            <input type="text" placeholder="Line description..." class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5">
                        </td>
                        <td class="py-3 pr-2">
                            <input type="number" step="0.01" placeholder="0.00" class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5 text-right bg-gray-50 debit-input">
                        </td>
                        <td class="py-3 pr-2">
                            <input type="number" step="0.01" value="4500.00" class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5 text-right font-medium text-gray-900 credit-input">
                        </td>
                        <td class="py-3 text-center">
                            <button type="button" class="text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 border-t-2 border-gray-300">
                        <td colspan="2" class="py-3 px-4 text-right font-bold text-gray-700">Totals:</td>
                        <td class="py-3 pr-2 text-right font-black text-gray-900">4,500.00</td>
                        <td class="py-3 pr-2 text-right font-black text-gray-900">4,500.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="mt-4 flex gap-3">
                <button type="button" class="text-sm font-medium text-nour-primary hover:text-nour-dark bg-nour-primary/10 px-3 py-1.5 rounded transition-colors">
                    <i class="fas fa-plus mr-1"></i> Add Line
                </button>
                <button type="button" class="text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded transition-colors">
                    Clear Lines
                </button>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <button type="button" class="px-5 py-2.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">Cancel</button>
        <button type="submit" name="action" value="draft" class="px-5 py-2.5 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-900 transition-colors shadow-sm">Save as Draft</button>
        <button type="submit" name="action" value="post" class="btn-primary px-8 shadow-lg">Post Entry</button>
    </div>
</form>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>