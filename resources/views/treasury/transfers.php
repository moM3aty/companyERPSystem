<?php
// Path: resources/views/treasury/transfers.php
$pageTitle = 'Treasury Transfers';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Internal Fund Transfers</h1>
        <p class="text-sm text-gray-500 mt-1">Move cash between bank accounts or petty cash boxes securely.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md flex items-center">
            <i class="fas fa-exchange-alt mr-2"></i> New Transfer
        </button>
    </div>
</div>

<div class="card p-0 overflow-visible relative">
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Date & Ref</th>
                    <th class="p-4 font-semibold">From Account (Source)</th>
                    <th class="p-4 font-semibold text-center"><i class="fas fa-arrow-right"></i></th>
                    <th class="p-4 font-semibold">To Account (Destination)</th>
                    <th class="p-4 font-semibold text-right">Amount</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4">
                        <span class="block font-bold text-nour-primary">TRF-2026-001</span>
                        <span class="text-xs text-gray-500">Aug 15, 2026</span>
                    </td>
                    <td class="p-4 font-medium text-gray-800">Al Rajhi Bank (Main)</td>
                    <td class="p-4 text-center text-gray-300"><i class="fas fa-long-arrow-alt-right"></i></td>
                    <td class="p-4 font-medium text-gray-800">HQ Petty Cash</td>
                    <td class="p-4 text-right font-bold text-gray-900">$2,500.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-[10px] uppercase font-bold">Completed</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>