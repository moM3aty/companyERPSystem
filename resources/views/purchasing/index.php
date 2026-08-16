<?php
// Path: resources/views/purchasing/index.php
$pageTitle = 'Purchasing & Procurement Dashboard';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Purchasing & Procurement</h1>
        <p class="text-sm text-gray-500 mt-1">Manage suppliers, purchase orders, and goods receipts.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-file-import mr-2 text-gray-400"></i> Import Suppliers
        </button>
        <a href="/purchasing/orders/create" class="btn-primary shadow-md flex items-center">
            <i class="fas fa-plus mr-2"></i> New Purchase Order
        </a>
    </div>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col items-center text-center">
        <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl mb-3"><i class="fas fa-truck"></i></div>
        <h4 class="text-2xl font-bold text-gray-900">48</h4>
        <p class="text-xs font-semibold text-gray-500 uppercase mt-1">Active Suppliers</p>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col items-center text-center">
        <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl mb-3"><i class="fas fa-file-signature"></i></div>
        <h4 class="text-2xl font-bold text-gray-900">12</h4>
        <p class="text-xs font-semibold text-gray-500 uppercase mt-1">Pending Approvals (PR)</p>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col items-center text-center border-b-4 border-b-nour-primary">
        <div class="w-12 h-12 rounded-full bg-nour-light/20 text-nour-primary flex items-center justify-center text-xl mb-3"><i class="fas fa-shopping-cart"></i></div>
        <h4 class="text-2xl font-bold text-gray-900">$184.2K</h4>
        <p class="text-xs font-semibold text-gray-500 uppercase mt-1">Purchases (MTD)</p>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col items-center text-center">
        <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-xl mb-3"><i class="fas fa-exclamation-triangle"></i></div>
        <h4 class="text-2xl font-bold text-gray-900">3</h4>
        <p class="text-xs font-semibold text-gray-500 uppercase mt-1">Delayed Deliveries</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Active Purchase Orders -->
    <div class="card p-0 overflow-hidden flex flex-col">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Open Purchase Orders</h3>
            <a href="/purchasing/orders" class="text-sm font-medium text-nour-primary hover:underline">View All</a>
        </div>
        <div class="table-responsive">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-white text-gray-500 text-[10px] uppercase tracking-wider border-b border-gray-200">
                    <tr>
                        <th class="p-3 font-semibold">PO Number</th>
                        <th class="p-3 font-semibold">Vendor</th>
                        <th class="p-3 font-semibold text-right">Amount</th>
                        <th class="p-3 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-nour-primary">PO-2026-089</td>
                        <td class="p-3 font-medium text-gray-800">Global Tech Suppliers</td>
                        <td class="p-3 text-right font-bold">$45,200.00</td>
                        <td class="p-3 text-center"><span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Sent</span></td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-nour-primary">PO-2026-088</td>
                        <td class="p-3 font-medium text-gray-800">Alpha Manufacturing</td>
                        <td class="p-3 text-right font-bold">$12,400.00</td>
                        <td class="p-3 text-center"><span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Partial</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Goods Receipts Pending Bill -->
    <div class="card p-0 overflow-hidden flex flex-col">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Receipts Awaiting Bills</h3>
            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full font-bold">Action Req.</span>
        </div>
        <div class="table-responsive">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-white text-gray-500 text-[10px] uppercase tracking-wider border-b border-gray-200">
                    <tr>
                        <th class="p-3 font-semibold">GRN No.</th>
                        <th class="p-3 font-semibold">Vendor</th>
                        <th class="p-3 font-semibold">Date Received</th>
                        <th class="p-3 font-semibold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-gray-700">GRN-2026-080</td>
                        <td class="p-3 font-medium text-gray-800">Global Tech Suppliers</td>
                        <td class="p-3 text-gray-500">Aug 16, 2026</td>
                        <td class="p-3 text-center"><button class="text-xs font-bold text-nour-primary hover:underline">Create Bill</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>