<?php
// Path: resources/views/treasury/dashboard.php
$pageTitle = 'Treasury & Bank Management';
ob_start();
?>

<!-- Header & Breadcrumbs -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a>
                </li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Treasury</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Cash & Bank Management</h1>
        <p class="text-sm text-gray-500 mt-1">Monitor liquidity, bank accounts, and process internal transfers.</p>
    </div>
    
    <div class="mt-4 sm:mt-0 flex gap-3">
        <a href="/accounting/reconciliation" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors flex items-center">
            <i class="fas fa-check-double mr-2 text-gray-400"></i> Bank Reconciliation
        </a>
        <button class="btn-primary flex items-center shadow-md shadow-nour-primary/30">
            <i class="fas fa-exchange-alt mr-2"></i> Internal Transfer
        </button>
    </div>
</div>

<!-- Liquidity Overview Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card p-6 border-t-4 border-t-blue-500 relative overflow-hidden">
        <div class="absolute right-0 top-0 mt-4 mr-4 text-blue-100"><i class="fas fa-university text-5xl"></i></div>
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 relative z-10">Total Bank Balances</p>
        <h3 class="text-3xl font-bold text-gray-900 relative z-10">$1,845,200.00</h3>
        <p class="text-sm text-green-500 mt-2 relative z-10 font-medium"><i class="fas fa-arrow-up mr-1"></i> 2.4% vs last week</p>
    </div>

    <div class="card p-6 border-t-4 border-t-green-500 relative overflow-hidden">
        <div class="absolute right-0 top-0 mt-4 mr-4 text-green-100"><i class="fas fa-money-bill-wave text-5xl"></i></div>
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 relative z-10">Total Cash on Hand (Petty Cash)</p>
        <h3 class="text-3xl font-bold text-gray-900 relative z-10">$12,450.00</h3>
        <p class="text-sm text-gray-500 mt-2 relative z-10">Across 4 branch registers</p>
    </div>

    <div class="card p-6 border-t-4 border-t-yellow-500 relative overflow-hidden">
        <div class="absolute right-0 top-0 mt-4 mr-4 text-yellow-100"><i class="fas fa-clock text-5xl"></i></div>
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 relative z-10">Pending Transfers / Deposits</p>
        <h3 class="text-3xl font-bold text-gray-900 relative z-10">$45,000.00</h3>
        <p class="text-sm text-gray-500 mt-2 relative z-10">Awaiting bank clearance</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Bank Accounts List -->
    <div class="lg:col-span-2 card p-0 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="text-base font-bold text-gray-800">Active Accounts</h3>
            <button class="text-sm text-nour-primary font-medium hover:underline">Add Account</button>
        </div>
        <div class="table-responsive">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                        <th class="p-4 font-semibold">Account Details</th>
                        <th class="p-4 font-semibold">GL Account</th>
                        <th class="p-4 font-semibold text-right">Ledger Balance</th>
                        <th class="p-4 font-semibold text-center">Status</th>
                        <th class="p-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 text-xl"><i class="fas fa-building-columns"></i></div>
                                <div>
                                    <span class="font-bold text-gray-900 block">Al Rajhi Bank (Main)</span>
                                    <span class="text-xs text-gray-500 font-mono">SA01800000001234567890</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-gray-600 font-mono text-xs">1010-01</td>
                        <td class="p-4 text-right font-bold text-gray-900">$1,450,000.00</td>
                        <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 text-[10px] uppercase font-bold rounded">Active</span></td>
                        <td class="p-4 text-right">
                            <button class="text-gray-400 hover:text-nour-primary px-2" title="View Statement"><i class="fas fa-list"></i></button>
                            <button class="text-gray-400 hover:text-blue-500 px-2" title="Edit"><i class="far fa-edit"></i></button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded bg-green-50 text-green-600 flex items-center justify-center flex-shrink-0 text-xl"><i class="fas fa-building-columns"></i></div>
                                <div>
                                    <span class="font-bold text-gray-900 block">SABB (Payroll)</span>
                                    <span class="text-xs text-gray-500 font-mono">SA05500000009876543210</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-gray-600 font-mono text-xs">1010-02</td>
                        <td class="p-4 text-right font-bold text-gray-900">$395,200.00</td>
                        <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 text-[10px] uppercase font-bold rounded">Active</span></td>
                        <td class="p-4 text-right">
                            <button class="text-gray-400 hover:text-nour-primary px-2" title="View Statement"><i class="fas fa-list"></i></button>
                            <button class="text-gray-400 hover:text-blue-500 px-2" title="Edit"><i class="far fa-edit"></i></button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded bg-yellow-50 text-yellow-600 flex items-center justify-center flex-shrink-0 text-xl"><i class="fas fa-box"></i></div>
                                <div>
                                    <span class="font-bold text-gray-900 block">HQ Petty Cash</span>
                                    <span class="text-xs text-gray-500">Safe/Vault 01</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-gray-600 font-mono text-xs">1020-01</td>
                        <td class="p-4 text-right font-bold text-gray-900">$5,000.00</td>
                        <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 text-[10px] uppercase font-bold rounded">Active</span></td>
                        <td class="p-4 text-right">
                            <button class="text-gray-400 hover:text-nour-primary px-2" title="View Statement"><i class="fas fa-list"></i></button>
                            <button class="text-gray-400 hover:text-blue-500 px-2" title="Edit"><i class="far fa-edit"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Transfers/Transactions -->
    <div class="lg:col-span-1 card p-0 flex flex-col">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="text-base font-bold text-gray-800">Recent Movements</h3>
        </div>
        <div class="p-0 flex-1">
            <ul class="divide-y divide-gray-100">
                <li class="p-4 hover:bg-gray-50 transition-colors cursor-pointer">
                    <div class="flex justify-between items-start mb-1">
                        <span class="font-bold text-gray-800 text-sm">Internal Transfer</span>
                        <span class="font-bold text-gray-900">$45,000.00</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-500">Main Bank <i class="fas fa-arrow-right mx-1 text-gray-300"></i> Payroll Bank</span>
                        <span class="text-yellow-600 font-medium bg-yellow-50 px-1.5 py-0.5 rounded">Pending</span>
                    </div>
                </li>
                <li class="p-4 hover:bg-gray-50 transition-colors cursor-pointer">
                    <div class="flex justify-between items-start mb-1">
                        <span class="font-bold text-gray-800 text-sm">Petty Cash Replenishment</span>
                        <span class="font-bold text-gray-900">$2,500.00</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-500">Main Bank <i class="fas fa-arrow-right mx-1 text-gray-300"></i> HQ Safe</span>
                        <span class="text-green-600 font-medium bg-green-50 px-1.5 py-0.5 rounded">Completed</span>
                    </div>
                </li>
                <li class="p-4 hover:bg-gray-50 transition-colors cursor-pointer">
                    <div class="flex justify-between items-start mb-1">
                        <span class="font-bold text-gray-800 text-sm">Owner Draw (Dividend)</span>
                        <span class="font-bold text-red-600">-$100,000.00</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-500">Main Bank <i class="fas fa-arrow-right mx-1 text-gray-300"></i> External</span>
                        <span class="text-green-600 font-medium bg-green-50 px-1.5 py-0.5 rounded">Completed</span>
                    </div>
                </li>
            </ul>
        </div>
        <div class="p-3 bg-gray-50 border-t border-gray-100 text-center">
            <a href="#" class="text-sm font-medium text-nour-primary hover:underline">View All Transactions</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>