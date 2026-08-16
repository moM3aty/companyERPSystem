<?php
// Path: resources/views/accounting/chart-of-accounts.php

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
$pageTitle = 'Chart of Accounts (COA)';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Chart of Accounts</h1>
        <p class="text-sm text-gray-500 mt-1">Manage your general ledger accounts, groups, and financial structure.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50">
            <i class="fas fa-file-import mr-2"></i> Import COA
        </button>
        <button class="btn-primary shadow-md">
            <i class="fas fa-plus mr-2"></i> Add Account
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-5 gap-6">
    <!-- Account Types Sidebar -->
    <div class="md:col-span-1 space-y-2">
        <button class="w-full text-left px-4 py-3 bg-nour-primary text-white font-bold rounded-lg shadow-md flex justify-between items-center">
            <span>All Accounts</span>
            <span class="bg-white/20 px-2 py-0.5 rounded text-xs">142</span>
        </button>
        <button class="w-full text-left px-4 py-3 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors flex justify-between items-center">
            <span><i class="fas fa-landmark text-blue-500 mr-2 w-4"></i> Assets</span>
            <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs">45</span>
        </button>
        <button class="w-full text-left px-4 py-3 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors flex justify-between items-center">
            <span><i class="fas fa-hand-holding-usd text-red-500 mr-2 w-4"></i> Liabilities</span>
            <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs">32</span>
        </button>
        <button class="w-full text-left px-4 py-3 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors flex justify-between items-center">
            <span><i class="fas fa-chart-pie text-purple-500 mr-2 w-4"></i> Equity</span>
            <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs">8</span>
        </button>
        <button class="w-full text-left px-4 py-3 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors flex justify-between items-center">
            <span><i class="fas fa-arrow-up text-green-500 mr-2 w-4"></i> Revenue</span>
            <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs">15</span>
        </button>
        <button class="w-full text-left px-4 py-3 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors flex justify-between items-center">
            <span><i class="fas fa-arrow-down text-orange-500 mr-2 w-4"></i> Expenses</span>
            <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs">42</span>
        </button>
    </div>

    <!-- COA Table -->
    <div class="md:col-span-4">
        <div class="card p-0 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex gap-4">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                    <input type="text" placeholder="Search by Code or Name..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary text-sm">
                </div>
                <button class="bg-white border border-gray-300 px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Collapse All</button>
            </div>
            
            <div class="table-responsive">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                            <th class="p-3 w-8"></th>
                            <th class="p-3 font-semibold">Account Code & Name</th>
                            <th class="p-3 font-semibold">Type</th>
                            <th class="p-3 font-semibold text-right">Current Balance</th>
                            <th class="p-3 font-semibold text-center">Status</th>
                            <th class="p-3 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <!-- Group Header -->
                        <tr class="bg-gray-50 border-b border-gray-100 font-bold text-gray-800 cursor-pointer hover:bg-gray-100 transition-colors">
                            <td class="p-3 text-center"><i class="fas fa-chevron-down text-gray-400 text-xs"></i></td>
                            <td class="p-3" colspan="2"><span class="text-nour-primary mr-2">1000</span> Current Assets</td>
                            <td class="p-3 text-right">$1,245,000.00</td>
                            <td class="p-3"></td>
                            <td class="p-3"></td>
                        </tr>
                        
                        <!-- Child Account -->
                        <tr class="border-b border-gray-50 hover:bg-blue-50/30 transition-colors">
                            <td class="p-3"></td>
                            <td class="p-3 pl-8 flex items-center">
                                <i class="fas fa-level-up-alt rotate-90 text-gray-300 mr-2"></i>
                                <span class="font-mono text-gray-500 mr-3">1010</span>
                                <span class="font-medium text-gray-900">Cash in Bank - Main (SAR)</span>
                            </td>
                            <td class="p-3 text-gray-600">Bank & Cash</td>
                            <td class="p-3 text-right font-semibold text-gray-900">$450,200.00</td>
                            <td class="p-3 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-[10px] uppercase font-bold">Active</span></td>
                            <td class="p-3 text-right">
                                <button class="text-gray-400 hover:text-nour-primary px-1" title="Ledger"><i class="fas fa-book"></i></button>
                                <button class="text-gray-400 hover:text-blue-500 px-1 ml-1" title="Edit"><i class="far fa-edit"></i></button>
                            </td>
                        </tr>

                        <!-- Child Account -->
                        <tr class="border-b border-gray-50 hover:bg-blue-50/30 transition-colors">
                            <td class="p-3"></td>
                            <td class="p-3 pl-8 flex items-center">
                                <i class="fas fa-level-up-alt rotate-90 text-gray-300 mr-2"></i>
                                <span class="font-mono text-gray-500 mr-3">1200</span>
                                <span class="font-medium text-gray-900">Accounts Receivable (A/R)</span>
                                <span class="ml-2 text-[10px] bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded uppercase font-bold" title="System Control Account">Ctrl</span>
                            </td>
                            <td class="p-3 text-gray-600">Receivable</td>
                            <td class="p-3 text-right font-semibold text-gray-900">$794,800.00</td>
                            <td class="p-3 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-[10px] uppercase font-bold">Active</span></td>
                            <td class="p-3 text-right">
                                <button class="text-gray-400 hover:text-nour-primary px-1" title="Ledger"><i class="fas fa-book"></i></button>
                            </td>
                        </tr>

                        <!-- Group Header -->
                        <tr class="bg-gray-50 border-b border-gray-100 border-t border-t-gray-200 font-bold text-gray-800 cursor-pointer hover:bg-gray-100 transition-colors">
                            <td class="p-3 text-center"><i class="fas fa-chevron-right text-gray-400 text-xs"></i></td>
                            <td class="p-3" colspan="2"><span class="text-nour-primary mr-2">4000</span> Sales & Revenues</td>
                            <td class="p-3 text-right">$0.00</td>
                            <td class="p-3"></td>
                            <td class="p-3"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>