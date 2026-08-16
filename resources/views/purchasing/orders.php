<?php
// Path: resources/views/purchasing/orders.php
$pageTitle = 'Purchase Orders';
ob_start();
?>

<!-- Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i> Home</a></li>
                <li><i class="fas fa-chevron-right text-xs mx-2"></i><span>Purchasing</span></li>
                <li><i class="fas fa-chevron-right text-xs mx-2"></i><span class="text-gray-800 font-medium">Purchase Orders</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Purchase Orders (PO)</h1>
        <p class="text-sm text-gray-500 mt-1">Manage vendor orders, track deliveries, and process bills.</p>
    </div>
    
    <div class="mt-4 sm:mt-0 flex gap-3">
        <button class="btn-primary flex items-center shadow-md">
            <i class="fas fa-plus mr-2"></i> Create New PO
        </button>
    </div>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-blue-50 text-nour-primary flex items-center justify-center text-xl flex-shrink-0"><i class="fas fa-file-contract"></i></div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Draft / Pending</p>
            <h4 class="text-xl font-bold text-gray-800">12</h4>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl flex-shrink-0"><i class="fas fa-truck-loading"></i></div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Awaiting Receipt</p>
            <h4 class="text-xl font-bold text-gray-800">8</h4>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-xl flex-shrink-0"><i class="fas fa-clock"></i></div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Late Deliveries</p>
            <h4 class="text-xl font-bold text-gray-800">3</h4>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-green-50 text-green-600 flex items-center justify-center text-xl flex-shrink-0"><i class="fas fa-money-check-alt"></i></div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Purchases (MTD)</p>
            <h4 class="text-xl font-bold text-gray-800">$184.2K</h4>
        </div>
    </div>
</div>

<!-- PO Table -->
<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <div class="flex gap-2">
            <input type="text" placeholder="Search PO or Vendor..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-64">
        </div>
        <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600">
            <option>All Statuses</option>
            <option>Draft</option>
            <option>Sent</option>
            <option>Partially Received</option>
            <option>Completed</option>
        </select>
    </div>
    <div class="table-responsive rounded-b-lg">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">PO Number</th>
                    <th class="p-4 font-semibold">Vendor</th>
                    <th class="p-4 font-semibold">Order Date</th>
                    <th class="p-4 font-semibold">Expected Date</th>
                    <th class="p-4 font-semibold text-right">Total Amount</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4"><a href="/purchasing/orders/1" class="font-bold text-nour-primary hover:underline">PO-2026-089</a></td>
                    <td class="p-4">
                        <span class="font-semibold text-gray-900">Global Tech Suppliers</span>
                        <p class="text-[10px] text-gray-500">Ref: QT-9902</p>
                    </td>
                    <td class="p-4 text-gray-600">Aug 10, 2026</td>
                    <td class="p-4 font-medium text-gray-800">Aug 18, 2026</td>
                    <td class="p-4 text-right font-bold text-gray-900">$45,200.00</td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-yellow-100 text-yellow-800">Sent / Waiting</span>
                    </td>
                    <td class="p-4 text-right">
                        <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-2 py-1 rounded text-xs font-medium transition-colors shadow-sm">Receive Goods</button>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4"><a href="/purchasing/orders/2" class="font-bold text-nour-primary hover:underline">PO-2026-088</a></td>
                    <td class="p-4">
                        <span class="font-semibold text-gray-900">Alpha Manufacturing</span>
                    </td>
                    <td class="p-4 text-gray-600">Aug 05, 2026</td>
                    <td class="p-4 font-medium text-red-600">Aug 12, 2026 <span class="text-[10px] block text-red-400">Late (4 days)</span></td>
                    <td class="p-4 text-right font-bold text-gray-900">$12,400.00</td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-800">Partially Received</span>
                    </td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary p-1"><i class="fas fa-file-pdf"></i></button>
                        <button class="text-gray-400 hover:text-gray-700 p-1 ml-1"><i class="fas fa-ellipsis-v"></i></button>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors group opacity-75">
                    <td class="p-4"><a href="/purchasing/orders/3" class="font-bold text-gray-500 hover:text-nour-primary hover:underline">PO-2026-085</a></td>
                    <td class="p-4">
                        <span class="font-semibold text-gray-700">Office Supplies Co.</span>
                    </td>
                    <td class="p-4 text-gray-500">Jul 28, 2026</td>
                    <td class="p-4 text-gray-500">Aug 02, 2026</td>
                    <td class="p-4 text-right font-medium text-gray-700">$1,250.00</td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-green-100 text-green-800">Completed & Billed</span>
                    </td>
                    <td class="p-4 text-right">
                        <span class="text-xs text-green-600 font-medium"><i class="fas fa-check mr-1"></i> Closed</span>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>