<?php
// Path: resources/views/enterprise/intercompany/transactions.php
$pageTitle = 'Intercompany Transactions';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Cross-Company Transactions Log</h1>
        <p class="text-sm text-gray-500 mt-1">Audit log of all automated billing between group subsidiaries.</p>
    </div>
    <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
        <i class="fas fa-download mr-2"></i> Export Log
    </button>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-4 border-b border-gray-100 flex gap-4 bg-gray-50/50">
        <input type="text" placeholder="Search Reference..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-64">
        <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-48">
            <option>All Types</option>
            <option>Sales (AR)</option>
            <option>Purchases (AP)</option>
            <option>Service Fees</option>
        </select>
    </div>
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Date</th>
                    <th class="p-4 font-semibold">Reference</th>
                    <th class="p-4 font-semibold">Source Entity</th>
                    <th class="p-4 font-semibold text-center"><i class="fas fa-arrow-right"></i></th>
                    <th class="p-4 font-semibold">Destination Entity</th>
                    <th class="p-4 font-semibold text-right">Amount (Base)</th>
                    <th class="p-4 font-semibold text-center">Reconciliation</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50">
                    <td class="p-4 text-gray-500">Aug 28, 2026</td>
                    <td class="p-4 font-bold text-nour-primary">INV-IC-1024</td>
                    <td class="p-4 font-medium">Nour Logistics UAE</td>
                    <td class="p-4 text-center text-gray-400"><i class="fas fa-long-arrow-alt-right"></i></td>
                    <td class="p-4 font-medium">Nour KSA</td>
                    <td class="p-4 text-right font-bold text-gray-900">$12,500.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 text-[10px] uppercase font-bold rounded">Matched</span></td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="p-4 text-gray-500">Aug 25, 2026</td>
                    <td class="p-4 font-bold text-nour-primary">INV-IC-1023</td>
                    <td class="p-4 font-medium">Alpha Manufacturing</td>
                    <td class="p-4 text-center text-gray-400"><i class="fas fa-long-arrow-alt-right"></i></td>
                    <td class="p-4 font-medium">Nour KSA</td>
                    <td class="p-4 text-right font-bold text-gray-900">$45,000.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-[10px] uppercase font-bold rounded">Pending AP</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>