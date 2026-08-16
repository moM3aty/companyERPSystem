<?php
// Path: resources/views/enterprise/intercompany/reconciliation.php
$pageTitle = 'Intercompany Auto-Match';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Intercompany Reconciliation</h1>
        <p class="text-sm text-gray-500 mt-1">Match Accounts Receivable (AR) and Accounts Payable (AP) between sister companies.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50">
            <i class="fas fa-file-export mr-2"></i> Export Report
        </button>
        <button class="btn-primary shadow-md">
            <i class="fas fa-magic mr-2"></i> Run Auto-Match
        </button>
    </div>
</div>

<div class="card p-0 overflow-hidden mb-6">
    <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Reconciliation Period: <span class="text-nour-primary">Q3 - August 2026</span></h3>
        <span class="text-xs font-bold text-yellow-600 bg-yellow-100 px-3 py-1 rounded-full uppercase tracking-wider">Variances Detected</span>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-200 text-center">
        <div class="p-6 bg-white">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nour Enterprise KSA (AR)</p>
            <p class="text-3xl font-black text-gray-900">$1,450,200.00</p>
            <p class="text-xs text-gray-400 mt-1">Billed to UAE branch</p>
        </div>
        <div class="p-6 bg-white">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nour Logistics UAE (AP)</p>
            <p class="text-3xl font-black text-gray-900">$1,445,000.00</p>
            <p class="text-xs text-gray-400 mt-1">Received from KSA branch</p>
        </div>
        <div class="p-6 bg-red-50/50">
            <p class="text-xs font-bold text-red-500 uppercase tracking-wider mb-2">Unmatched Variance</p>
            <p class="text-3xl font-black text-red-600">$5,200.00</p>
            <p class="text-xs text-red-400 mt-1">Requires manual resolution</p>
        </div>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex gap-4">
        <input type="text" placeholder="Search Reference Number..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-64">
        <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600">
            <option>Unmatched Only</option>
            <option>Matched</option>
            <option>All Transactions</option>
        </select>
    </div>
    
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Reference No.</th>
                    <th class="p-4 font-semibold">Transaction Date</th>
                    <th class="p-4 font-semibold text-right">KSA AR Amount</th>
                    <th class="p-4 font-semibold text-right">UAE AP Amount</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-nour-primary">INV-IC-1025</td>
                    <td class="p-4 text-gray-600">Aug 28, 2026</td>
                    <td class="p-4 text-right font-medium text-gray-900">$5,200.00</td>
                    <td class="p-4 text-right text-gray-400 font-mono">Missing</td>
                    <td class="p-4 text-center">
                        <span class="text-red-500 font-bold text-[10px] uppercase bg-red-50 px-2 py-0.5 rounded border border-red-200">Variance</span>
                    </td>
                    <td class="p-4 text-right">
                        <button class="text-xs bg-white border border-gray-300 px-2 py-1 rounded hover:bg-gray-50 font-medium">Resolve</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors opacity-75">
                    <td class="p-4 font-bold text-gray-600">INV-IC-1024</td>
                    <td class="p-4 text-gray-500">Aug 25, 2026</td>
                    <td class="p-4 text-right font-medium text-gray-600">$12,500.00</td>
                    <td class="p-4 text-right font-medium text-gray-600">$12,500.00</td>
                    <td class="p-4 text-center">
                        <span class="text-green-600 font-bold text-[10px] uppercase bg-green-50 px-2 py-0.5 rounded border border-green-200">Matched</span>
                    </td>
                    <td class="p-4 text-right text-gray-400">
                        <i class="fas fa-lock"></i>
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