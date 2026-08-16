<?php
// Path: resources/views/accounting/index.php

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
$pageTitle = 'Accounting & Finance Dashboard';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Accounting & Finance</h1>
        <p class="text-sm text-gray-500 mt-1">General Ledger, Accounts Payable, Accounts Receivable, and Reporting.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <a href="<?= $appUrlEscaped('accounting/journal-entries/create') ?>" class="btn-primary shadow-md flex items-center">
            <i class="fas fa-plus mr-2"></i> New Journal Entry
        </a>
    </div>
</div>

<!-- Main Financial KPIs -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="card p-5 border-t-4 border-t-blue-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Cash & Bank Balance</p>
        <h3 class="text-2xl font-black text-gray-900">$1,845,200.00</h3>
        <p class="text-xs text-green-500 mt-2 font-bold"><i class="fas fa-arrow-up mr-1"></i> Highly Liquid</p>
    </div>
    <div class="card p-5 border-t-4 border-t-green-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Accounts Receivable (A/R)</p>
        <h3 class="text-2xl font-black text-gray-900">$450,300.00</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">Expected inward cash flow</p>
    </div>
    <div class="card p-5 border-t-4 border-t-red-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Accounts Payable (A/P)</p>
        <h3 class="text-2xl font-black text-gray-900">$215,400.00</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">Pending supplier payments</p>
    </div>
    <div class="card p-5 border-t-4 border-t-purple-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Net Income (MTD)</p>
        <h3 class="text-2xl font-black text-gray-900">$85,420.00</h3>
        <p class="text-xs text-green-500 mt-2 font-bold"><i class="fas fa-arrow-up mr-1"></i> Profitable Period</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- P&L Chart Placeholder -->
    <div class="card col-span-1 lg:col-span-2 p-5 flex flex-col">
        <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-3">
            <h3 class="text-lg font-bold text-gray-800">Income vs Expenses (YTD)</h3>
            <a href="<?= $appUrlEscaped('reports') ?>" class="text-sm font-medium text-nour-primary hover:underline">Full Report</a>
        </div>
        <div class="flex-1 bg-gray-50 rounded-lg border border-dashed border-gray-200 flex items-center justify-center min-h-[300px] relative">
            <i class="fas fa-chart-bar text-5xl text-gray-300 absolute opacity-50"></i>
            <span class="text-gray-500 font-medium z-10 bg-white/80 px-3 py-1 rounded border border-gray-200 text-xs">Financial Chart Data Viz Here</span>
        </div>
    </div>

    <!-- Recent Journal Entries -->
    <div class="card col-span-1 p-0 flex flex-col overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Recent Postings</h3>
            <a href="<?= $appUrlEscaped('accounting/journal-entries') ?>" class="text-sm font-medium text-nour-primary hover:underline">View Ledger</a>
        </div>
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <ul class="divide-y divide-gray-100">
                <li class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-start mb-1">
                        <span class="font-bold text-nour-primary text-sm">JE-2026-08012</span>
                        <span class="font-bold text-gray-900 text-sm">$28,175.00</span>
                    </div>
                    <p class="text-xs text-gray-600 mb-1">Sales Invoice Recognition (INV-089)</p>
                    <span class="text-[10px] text-gray-400"><i class="far fa-clock mr-1"></i> Today</span>
                </li>
                <li class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-start mb-1">
                        <span class="font-bold text-nour-primary text-sm">JE-2026-08011</span>
                        <span class="font-bold text-gray-900 text-sm">$4,500.00</span>
                    </div>
                    <p class="text-xs text-gray-600 mb-1">Manual Expense Accrual</p>
                    <span class="text-[10px] text-gray-400"><i class="far fa-clock mr-1"></i> Yesterday</span>
                </li>
                <li class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-start mb-1">
                        <span class="font-bold text-nour-primary text-sm">JE-2026-08010</span>
                        <span class="font-bold text-gray-900 text-sm">$12,400.00</span>
                    </div>
                    <p class="text-xs text-gray-600 mb-1">Supplier Payment Voucher (PV-045)</p>
                    <span class="text-[10px] text-gray-400"><i class="far fa-clock mr-1"></i> Aug 14, 2026</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>