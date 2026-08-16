<?php
// Path: resources/views/purchasing/rfqs/index.php
$pageTitle = 'Requests for Quotation (RFQs)';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Requests for Quotation (RFQs)</h1>
        <p class="text-sm text-gray-500 mt-1">Manage vendor bids and negotiate the best prices before issuing POs.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="btn-primary shadow-md flex items-center">
            <i class="fas fa-plus mr-2"></i> New RFQ
        </button>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">RFQ Number & Title</th>
                    <th class="p-4 font-semibold">Deadline</th>
                    <th class="p-4 font-semibold text-center">Invited</th>
                    <th class="p-4 font-semibold text-center">Bids Received</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-blue-50/20 transition-colors group">
                    <td class="p-4">
                        <span class="font-bold text-nour-primary block">RFQ-2608-015</span>
                        <span class="text-xs text-gray-600">Q3 IT Equipment Restock</span>
                    </td>
                    <td class="p-4 font-bold text-red-500">Aug 20, 2026</td>
                    <td class="p-4 text-center font-bold text-gray-700">4</td>
                    <td class="p-4 text-center font-black text-green-600 text-lg">2</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-[10px] uppercase font-bold">Bidding Open</span></td>
                    <td class="p-4 text-right">
                        <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-2 py-1 rounded text-xs font-medium transition-colors shadow-sm">View Bids</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors group opacity-80">
                    <td class="p-4">
                        <span class="font-bold text-gray-600 block">RFQ-2607-042</span>
                        <span class="text-xs text-gray-500">Warehouse Forklifts</span>
                    </td>
                    <td class="p-4 text-gray-500">Jul 28, 2026</td>
                    <td class="p-4 text-center font-medium text-gray-500">3</td>
                    <td class="p-4 text-center font-medium text-gray-500">3</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-[10px] uppercase font-bold">Awarded</span></td>
                    <td class="p-4 text-right">
                        <span class="text-xs text-gray-400 font-medium">PO-2608-012 Generated</span>
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