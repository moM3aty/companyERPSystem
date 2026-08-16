<?php
// Path: resources/views/accounting/cost-centers/index.php

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
$pageTitle = 'Cost Centers & Analytics';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="<?= $appUrlEscaped('dashboard') ?>" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><a href="<?= $appUrlEscaped('accounting') ?>" class="hover:text-nour-primary transition-colors">Accounting</a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Cost Centers</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Cost Centers (Analytic Accounts)</h1>
        <p class="text-sm text-gray-500 mt-1">Track revenues and expenses per department, project, or branch for detailed P&L.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md flex items-center">
            <i class="fas fa-plus mr-2"></i> Add Cost Center
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Tree View -->
    <div class="lg:col-span-2 card p-0 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <input type="text" placeholder="Search Cost Center..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-64">
            <button class="text-sm text-gray-500 hover:text-nour-primary font-medium"><i class="fas fa-expand-alt mr-1"></i> Expand All</button>
        </div>
        
        <div class="table-responsive">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                        <th class="p-4 font-semibold">Hierarchy & Name</th>
                        <th class="p-4 font-semibold">Code</th>
                        <th class="p-4 font-semibold text-right text-green-600">Total Income (YTD)</th>
                        <th class="p-4 font-semibold text-right text-red-500">Total Expense (YTD)</th>
                        <th class="p-4 font-semibold text-right font-black">Margin</th>
                        <th class="p-4 font-semibold text-right w-10"></th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    <!-- Root Node -->
                    <tr class="bg-gray-50/50 hover:bg-gray-100 transition-colors cursor-pointer">
                        <td class="p-4 font-bold text-gray-900"><i class="fas fa-chevron-down mr-2 text-gray-400 text-xs"></i> 1. Branches & Locations</td>
                        <td class="p-4 font-mono text-gray-500 text-xs">CC-100</td>
                        <td class="p-4 text-right font-semibold text-green-600">$1,850,000</td>
                        <td class="p-4 text-right font-semibold text-red-500">$900,000</td>
                        <td class="p-4 text-right font-black text-gray-900">$950,000</td>
                        <td class="p-4 text-right"><button class="text-gray-400 hover:text-nour-primary"><i class="far fa-edit"></i></button></td>
                    </tr>
                    <!-- Child Node -->
                    <tr class="hover:bg-blue-50/20 transition-colors">
                        <td class="p-4 font-medium text-gray-800 pl-10"><i class="fas fa-level-up-alt rotate-90 text-gray-300 mr-2"></i> HQ - Riyadh</td>
                        <td class="p-4 font-mono text-gray-500 text-xs">CC-101</td>
                        <td class="p-4 text-right text-gray-700">$1,200,000</td>
                        <td class="p-4 text-right text-gray-700">$600,000</td>
                        <td class="p-4 text-right font-bold text-gray-900">$600,000</td>
                        <td class="p-4 text-right"><button class="text-gray-400 hover:text-nour-primary"><i class="far fa-edit"></i></button></td>
                    </tr>
                    <!-- Child Node -->
                    <tr class="hover:bg-blue-50/20 transition-colors">
                        <td class="p-4 font-medium text-gray-800 pl-10"><i class="fas fa-level-up-alt rotate-90 text-gray-300 mr-2"></i> Jeddah Branch</td>
                        <td class="p-4 font-mono text-gray-500 text-xs">CC-102</td>
                        <td class="p-4 text-right text-gray-700">$650,000</td>
                        <td class="p-4 text-right text-gray-700">$300,000</td>
                        <td class="p-4 text-right font-bold text-gray-900">$350,000</td>
                        <td class="p-4 text-right"><button class="text-gray-400 hover:text-nour-primary"><i class="far fa-edit"></i></button></td>
                    </tr>
                    
                    <!-- Root Node -->
                    <tr class="bg-gray-50/50 hover:bg-gray-100 transition-colors border-t-2 border-gray-200 cursor-pointer">
                        <td class="p-4 font-bold text-gray-900"><i class="fas fa-chevron-right mr-2 text-gray-400 text-xs"></i> 2. Projects & Implementations</td>
                        <td class="p-4 font-mono text-gray-500 text-xs">CC-200</td>
                        <td class="p-4 text-right font-semibold text-green-600">$45,000</td>
                        <td class="p-4 text-right font-semibold text-red-500">$22,500</td>
                        <td class="p-4 text-right font-black text-gray-900">$22,500</td>
                        <td class="p-4 text-right"><button class="text-gray-400 hover:text-nour-primary"><i class="far fa-edit"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Analysis -->
    <div class="lg:col-span-1 space-y-6">
        <div class="card p-5 border-t-4 border-t-nour-primary">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Margin by Cost Center (Top 3)</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-xs font-bold mb-1">
                        <span class="text-gray-700">HQ - Riyadh</span>
                        <span class="text-gray-900">63.1%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-nour-primary h-2 rounded-full" style="width: 63.1%"></div></div>
                </div>
                <div>
                    <div class="flex justify-between text-xs font-bold mb-1">
                        <span class="text-gray-700">Jeddah Branch</span>
                        <span class="text-gray-900">36.8%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-blue-400 h-2 rounded-full" style="width: 36.8%"></div></div>
                </div>
                <div>
                    <div class="flex justify-between text-xs font-bold mb-1">
                        <span class="text-gray-700">Projects Dept</span>
                        <span class="text-gray-900">0.1%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-yellow-400 h-2 rounded-full" style="width: 5%"></div></div>
                </div>
            </div>
            <button class="w-full mt-6 bg-gray-50 border border-gray-200 text-gray-700 py-2 rounded text-sm font-bold hover:bg-gray-100">View Full P&L Report</button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>