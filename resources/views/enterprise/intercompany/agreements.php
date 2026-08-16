<?php
// Path: resources/views/enterprise/intercompany/agreements.php
$pageTitle = 'Intercompany Agreements';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Transfer Pricing Agreements</h1>
        <p class="text-sm text-gray-500 mt-1">Configure automated markups and clearing accounts between sister companies.</p>
    </div>
    <button class="btn-primary shadow-md">
        <i class="fas fa-handshake mr-2"></i> New Agreement
    </button>
</div>

<div class="card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Selling Entity</th>
                    <th class="p-4 font-semibold text-center"><i class="fas fa-exchange-alt"></i></th>
                    <th class="p-4 font-semibold">Buying Entity</th>
                    <th class="p-4 font-semibold">Markup Policy</th>
                    <th class="p-4 font-semibold">Default Clearing Accounts</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-nour-dark">Nour Manufacturing</td>
                    <td class="p-4 text-center text-gray-300"><i class="fas fa-arrow-right"></i></td>
                    <td class="p-4 font-bold text-nour-primary">Nour Retail KSA</td>
                    <td class="p-4"><span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded font-bold">Cost + 5%</span></td>
                    <td class="p-4 text-xs text-gray-500">
                        <p>AR: 1205 (Interco Receivables)</p>
                        <p>AP: 2105 (Interco Payables)</p>
                    </td>
                    <td class="p-4 text-center"><span class="text-green-500"><i class="fas fa-check-circle text-lg"></i></span></td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-nour-dark">Nour Logistics UAE</td>
                    <td class="p-4 text-center text-gray-300"><i class="fas fa-arrow-right"></i></td>
                    <td class="p-4 font-bold text-nour-primary">Nour Retail KSA</td>
                    <td class="p-4"><span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded font-bold">Fixed Fee Rate</span></td>
                    <td class="p-4 text-xs text-gray-500">
                        <p>AR: 1206 (Interco Services)</p>
                        <p>AP: 2106 (Interco Accruals)</p>
                    </td>
                    <td class="p-4 text-center"><span class="text-green-500"><i class="fas fa-check-circle text-lg"></i></span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>