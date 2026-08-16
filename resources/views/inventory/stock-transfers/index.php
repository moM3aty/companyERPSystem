<?php
// Path: resources/views/inventory/stock-transfers/index.php
$pageTitle = 'Stock Transfers';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Internal Stock Transfers</h1>
        <p class="text-sm text-gray-500 mt-1">Track inventory movements between company branches and warehouses.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="btn-primary shadow-md">
            <i class="fas fa-exchange-alt mr-2"></i> New Transfer
        </button>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex gap-4">
        <input type="text" placeholder="Search Transfer No..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-64">
        <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600">
            <option>All Transfers</option>
            <option>In Transit</option>
            <option>Received (Completed)</option>
        </select>
    </div>
    <div class="table-responsive rounded-b-lg">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Transfer No.</th>
                    <th class="p-4 font-semibold">Date</th>
                    <th class="p-4 font-semibold">From Warehouse</th>
                    <th class="p-4 font-semibold text-center"><i class="fas fa-arrow-right"></i></th>
                    <th class="p-4 font-semibold">To Warehouse</th>
                    <th class="p-4 font-semibold text-center">Items</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 font-bold text-nour-primary">TRF-2608-011</td>
                    <td class="p-4 text-gray-600">Aug 16, 2026</td>
                    <td class="p-4 font-medium text-gray-800">HQ - Main Riyadh</td>
                    <td class="p-4 text-center text-gray-300"><i class="fas fa-long-arrow-alt-right"></i></td>
                    <td class="p-4 font-medium text-gray-800">Jeddah Distribution</td>
                    <td class="p-4 text-center font-bold text-gray-700">14</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-[10px] uppercase font-bold">In Transit</span></td>
                    <td class="p-4 text-right">
                        <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-2 py-1 rounded text-xs font-medium transition-colors shadow-sm">Receive</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors group opacity-80">
                    <td class="p-4 font-bold text-gray-600">TRF-2608-005</td>
                    <td class="p-4 text-gray-600">Aug 10, 2026</td>
                    <td class="p-4 font-medium text-gray-800">Jeddah Distribution</td>
                    <td class="p-4 text-center text-gray-300"><i class="fas fa-long-arrow-alt-right"></i></td>
                    <td class="p-4 font-medium text-gray-800">HQ - Main Riyadh</td>
                    <td class="p-4 text-center font-bold text-gray-700">2</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-[10px] uppercase font-bold">Completed</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary px-2"><i class="fas fa-print"></i></button>
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