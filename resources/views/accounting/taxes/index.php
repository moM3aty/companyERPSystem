<?php
// Path: resources/views/accounting/taxes/index.php

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
$pageTitle = 'Taxes & VAT Configuration';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Taxes & VAT Rules</h1>
        <p class="text-sm text-gray-500 mt-1">Configure tax rates, jurisdictions, and linked General Ledger accounts.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-file-pdf mr-2 text-red-500"></i> Generate VAT Return
        </button>
        <button class="btn-primary shadow-md">
            <i class="fas fa-plus mr-2"></i> New Tax Rule
        </button>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Tax Name / Description</th>
                    <th class="p-4 font-semibold">Scope</th>
                    <th class="p-4 font-semibold text-center">Rate (%)</th>
                    <th class="p-4 font-semibold">GL Account Mapping</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4">
                        <span class="font-bold text-gray-900 block">Standard VAT 15%</span>
                        <span class="text-xs text-gray-500">Saudi Arabia Standard Rate</span>
                    </td>
                    <td class="p-4"><span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-gray-200">Sales & Purchases</span></td>
                    <td class="p-4 text-center font-black text-nour-primary text-lg">15.00%</td>
                    <td class="p-4 text-xs text-gray-600 font-mono">
                        <div class="mb-1"><span class="text-gray-400 mr-1">Sales:</span> 2200 - VAT Payable</div>
                        <div><span class="text-gray-400 mr-1">Purch:</span> 1300 - VAT Receivable</div>
                    </td>
                    <td class="p-4 text-center"><span class="text-green-500"><i class="fas fa-check-circle text-lg" title="Active"></i></span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-blue-500 px-2"><i class="far fa-edit"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4">
                        <span class="font-bold text-gray-900 block">Zero Rated 0%</span>
                        <span class="text-xs text-gray-500">Exports & Specific Med/Edu</span>
                    </td>
                    <td class="p-4"><span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-gray-200">Sales Only</span></td>
                    <td class="p-4 text-center font-black text-gray-700 text-lg">0.00%</td>
                    <td class="p-4 text-xs text-gray-600 font-mono">
                        <div class="mb-1"><span class="text-gray-400 mr-1">Sales:</span> 2200 - VAT Payable</div>
                    </td>
                    <td class="p-4 text-center"><span class="text-green-500"><i class="fas fa-check-circle text-lg" title="Active"></i></span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-blue-500 px-2"><i class="far fa-edit"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors opacity-75">
                    <td class="p-4">
                        <span class="font-bold text-gray-900 block">Exempt</span>
                        <span class="text-xs text-gray-500">Financial Services / Real Estate</span>
                    </td>
                    <td class="p-4"><span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-gray-200">Sales & Purchases</span></td>
                    <td class="p-4 text-center font-medium text-gray-400">N/A</td>
                    <td class="p-4 text-xs text-gray-400 italic">No GL Mapping Required</td>
                    <td class="p-4 text-center"><span class="text-green-500"><i class="fas fa-check-circle text-lg" title="Active"></i></span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-blue-500 px-2"><i class="far fa-edit"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>