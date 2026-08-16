<?php
// Path: resources/views/accounting/reconciliation.php

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
$pageTitle = 'Bank Reconciliation';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Bank Reconciliation</h1>
        <p class="text-sm text-gray-500 mt-1">Match bank statement transactions with ERP ledger records.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50">
            <i class="fas fa-file-upload mr-2"></i> Upload Statement (MT940/CSV)
        </button>
    </div>
</div>

<div class="card p-4 mb-6 grid grid-cols-1 md:grid-cols-4 gap-6 items-center bg-white">
    <div class="col-span-1 md:col-span-2">
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Select Bank Account</label>
        <select class="w-full border-gray-300 rounded-md focus:ring-nour-primary font-medium text-gray-800">
            <option>1010 - Al Rajhi Bank (Main Operating) - SA018...</option>
            <option>1011 - SABB (Payroll) - SA055...</option>
        </select>
    </div>
    <div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Statement Ending Balance</p>
        <input type="text" value="450,200.00" class="w-full border-gray-300 rounded-md font-bold text-gray-900 text-right bg-gray-50">
    </div>
    <div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Difference</p>
        <div class="w-full py-2 px-3 border border-green-300 bg-green-50 rounded-md font-bold text-green-700 text-right">
            0.00 (Balanced)
        </div>
    </div>
</div>

<!-- Reconciliation Split View -->
<div class="flex flex-col lg:flex-row gap-6 h-[600px]">
    
    <!-- Left Pane: Bank Statement -->
    <div class="flex-1 card p-0 flex flex-col overflow-hidden border-t-4 border-t-blue-500">
        <div class="p-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center shadow-sm z-10">
            <h3 class="font-bold text-gray-800"><i class="fas fa-university text-blue-500 mr-2"></i> Bank Statement Lines</h3>
            <span class="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded font-semibold">12 Unmatched</span>
        </div>
        <div class="flex-1 overflow-y-auto bg-gray-50/30 p-2 space-y-2 custom-scrollbar">
            
            <!-- Bank Line -->
            <div class="bg-white border-l-4 border-l-transparent border border-gray-200 p-3 rounded shadow-sm hover:border-l-blue-400 cursor-pointer transition-all">
                <div class="flex justify-between items-start mb-1">
                    <span class="text-xs font-bold text-gray-500">Aug 14, 2026</span>
                    <span class="font-bold text-green-600">+ $12,500.00</span>
                </div>
                <p class="text-sm font-medium text-gray-800">INWARD TRF - ALPHA TECH SOLUTIONS</p>
                <p class="text-[10px] text-gray-400 font-mono mt-1">Ref: TRF-9988221</p>
            </div>

            <!-- Bank Line -->
            <div class="bg-white border-l-4 border-l-transparent border border-gray-200 p-3 rounded shadow-sm hover:border-l-blue-400 cursor-pointer transition-all">
                <div class="flex justify-between items-start mb-1">
                    <span class="text-xs font-bold text-gray-500">Aug 13, 2026</span>
                    <span class="font-bold text-red-600">- $4,500.00</span>
                </div>
                <p class="text-sm font-medium text-gray-800">POS SETTLEMENT CHARGES</p>
            </div>
            
            <!-- Matched Bank Line (Dimmed) -->
            <div class="bg-gray-100 border-l-4 border-l-green-400 border border-gray-200 p-3 rounded opacity-60">
                <div class="flex justify-between items-start mb-1">
                    <span class="text-xs font-bold text-gray-500">Aug 12, 2026</span>
                    <span class="font-bold text-gray-600">- $1,200.00</span>
                </div>
                <p class="text-sm font-medium text-gray-700">UTILITY BILL PMT</p>
                <div class="mt-2 text-[10px] font-bold text-green-600"><i class="fas fa-link mr-1"></i> Matched with JE-08005</div>
            </div>

        </div>
    </div>

    <!-- Center Action (Match Button) -->
    <div class="hidden lg:flex flex-col justify-center items-center px-2">
        <button class="w-12 h-12 bg-nour-primary text-white rounded-full shadow-lg hover:bg-nour-dark hover:scale-110 transition-all flex items-center justify-center text-xl focus:outline-none focus:ring-4 focus:ring-nour-light/30 disabled:opacity-50" title="Match Selected">
            <i class="fas fa-link"></i>
        </button>
        <button class="mt-4 text-xs font-bold text-gray-400 hover:text-nour-primary">Auto-Match</button>
    </div>

    <!-- Right Pane: System Ledger -->
    <div class="flex-1 card p-0 flex flex-col overflow-hidden border-t-4 border-t-nour-primary">
        <div class="p-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center shadow-sm z-10">
            <h3 class="font-bold text-gray-800"><i class="fas fa-book text-nour-primary mr-2"></i> ERP Ledger Lines</h3>
            <div class="flex gap-2">
                <input type="text" placeholder="Search Amount or Ref..." class="border border-gray-300 rounded px-2 py-0.5 text-xs w-32">
            </div>
        </div>
        <div class="flex-1 overflow-y-auto bg-gray-50/30 p-2 space-y-2 custom-scrollbar">
            
            <!-- Ledger Line -->
            <div class="bg-white border-l-4 border-l-transparent border border-gray-200 p-3 rounded shadow-sm hover:border-l-nour-primary cursor-pointer transition-all">
                <div class="flex justify-between items-start mb-1">
                    <span class="text-xs font-bold text-nour-primary">JE-2026-08015</span>
                    <span class="font-bold text-green-600">+ $12,500.00</span>
                </div>
                <p class="text-sm font-medium text-gray-800">Payment Received: INV-2026-0089</p>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-xs text-gray-500">Customer: Alpha Tech</span>
                    <span class="text-[10px] text-gray-400">Aug 14</span>
                </div>
            </div>

            <!-- Ledger Line -->
            <div class="bg-white border-l-4 border-l-transparent border border-gray-200 p-3 rounded shadow-sm hover:border-l-nour-primary cursor-pointer transition-all border-dashed">
                <div class="flex justify-center items-center py-4 text-gray-400 hover:text-nour-primary">
                    <i class="fas fa-plus-circle mr-2"></i> <span class="text-sm font-medium">Create Journal Entry on the fly</span>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>