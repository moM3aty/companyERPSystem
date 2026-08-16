<?php
// Path: resources/views/accounting/journal-entries/index.php

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
$pageTitle = 'Journal Entries';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Journal Entries (JEs)</h1>
        <p class="text-sm text-gray-500 mt-1">Review automated postings and create manual manual journal entries.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="<?= $appUrlEscaped('accounting/journal-entries/create') ?>" class="btn-primary shadow-md flex items-center">
            <i class="fas fa-plus mr-2"></i> Create Manual Entry
        </a>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <!-- Filters -->
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex flex-wrap gap-4 items-center justify-between">
        <div class="flex gap-4 flex-1">
            <input type="text" placeholder="Search Reference or Memo..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-64">
            <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600">
                <option value="">All Statuses</option>
                <option value="posted">Posted</option>
                <option value="draft">Draft / Unposted</option>
                <option value="voided">Voided</option>
            </select>
            <input type="date" class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600">
        </div>
        <button class="bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-md text-sm font-medium hover:bg-gray-100">
            <i class="fas fa-filter mr-1"></i> Filter
        </button>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Date</th>
                    <th class="p-4 font-semibold">Entry No.</th>
                    <th class="p-4 font-semibold">Source / Type</th>
                    <th class="p-4 font-semibold">Memo / Description</th>
                    <th class="p-4 font-semibold text-right">Total Amount</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 text-gray-600">Aug 15, 2026</td>
                    <td class="p-4 font-bold text-nour-primary">JE-2026-08012</td>
                    <td class="p-4"><span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-blue-100">Sales Invoice</span></td>
                    <td class="p-4 text-gray-700 truncate max-w-xs">Automated posting for Invoice INV-2026-0089</td>
                    <td class="p-4 text-right font-bold text-gray-900">28,175.00 SAR</td>
                    <td class="p-4 text-center"><span class="text-green-600 font-medium text-xs"><i class="fas fa-check-circle mr-1"></i> Posted</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary px-2"><i class="fas fa-eye"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors bg-gray-50/50">
                    <td class="p-4 text-gray-600">Aug 14, 2026</td>
                    <td class="p-4 font-bold text-nour-primary">JE-2026-08011</td>
                    <td class="p-4"><span class="bg-purple-50 text-purple-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-purple-100">Manual Entry</span></td>
                    <td class="p-4 text-gray-700 truncate max-w-xs">Accrued Office Expenses for August</td>
                    <td class="p-4 text-right font-bold text-gray-900">4,500.00 SAR</td>
                    <td class="p-4 text-center"><span class="text-yellow-600 font-medium text-xs bg-yellow-50 px-2 py-0.5 rounded"><i class="fas fa-pen mr-1"></i> Draft</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-blue-500 px-1" title="Edit"><i class="far fa-edit"></i></button>
                        <button class="text-green-500 hover:text-green-700 px-1 ml-1" title="Post Entry"><i class="fas fa-check"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 text-gray-600">Aug 14, 2026</td>
                    <td class="p-4 font-bold text-nour-primary">JE-2026-08010</td>
                    <td class="p-4"><span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-gray-200">Bill Payment</span></td>
                    <td class="p-4 text-gray-700 truncate max-w-xs">Payment to Vendor: Alpha Manufacturing</td>
                    <td class="p-4 text-right font-bold text-gray-900">12,400.00 SAR</td>
                    <td class="p-4 text-center"><span class="text-green-600 font-medium text-xs"><i class="fas fa-check-circle mr-1"></i> Posted</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary px-2"><i class="fas fa-eye"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination Mock -->
    <div class="p-4 border-t border-gray-200 bg-white flex justify-between items-center text-sm text-gray-500">
        <span>Showing 1 to 3 of 1,245 Entries</span>
        <div class="flex gap-1">
            <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">Prev</button>
            <button class="px-3 py-1 border border-gray-300 bg-nour-primary text-white rounded">1</button>
            <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">2</button>
            <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">Next</button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>