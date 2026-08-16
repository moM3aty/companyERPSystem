<?php
// Path: resources/views/inventory/products/show.php
$pageTitle = 'Product Details';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary"><i class="fas fa-home mr-1"></i></a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><a href="/inventory/products" class="hover:text-nour-primary">Products</a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">DLL-XPS-15</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Dell XPS 15 Laptop</h1>
        <p class="text-sm text-gray-500 mt-1 font-mono">SKU: DLL-XPS-15 | Barcode: 8712345678901</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50"><i class="fas fa-barcode mr-1"></i> Print Label</button>
        <a href="/inventory/products/1/edit" class="btn-primary shadow-md"><i class="far fa-edit mr-2"></i> Edit Product</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="card p-5 border-l-4 border-l-green-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">On Hand (Total)</p>
        <h3 class="text-3xl font-black text-gray-900">145 <span class="text-sm text-gray-500 font-normal">Units</span></h3>
    </div>
    <div class="card p-5 border-l-4 border-l-yellow-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Reserved / Pending</p>
        <h3 class="text-3xl font-black text-gray-900">12 <span class="text-sm text-gray-500 font-normal">Units</span></h3>
        <p class="text-xs text-gray-400 mt-1">In Sales Orders</p>
    </div>
    <div class="card p-5 border-l-4 border-l-nour-primary">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Available to Sell</p>
        <h3 class="text-3xl font-black text-nour-primary">133 <span class="text-sm text-gray-500 font-normal">Units</span></h3>
    </div>
    <div class="card p-5 border-l-4 border-l-purple-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Moving Average Cost</p>
        <h3 class="text-3xl font-black text-gray-900">$1,100.50</h3>
        <p class="text-xs text-green-500 mt-1 font-medium">Sales Price: $1,450.00</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left: Stock by Location -->
    <div class="lg:col-span-1 space-y-6">
        <div class="card p-0 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50"><h3 class="font-bold text-gray-800"><i class="fas fa-map-marker-alt text-nour-primary mr-2"></i> Stock by Warehouse</h3></div>
            <ul class="divide-y divide-gray-100 text-sm">
                <li class="p-4 flex justify-between items-center hover:bg-gray-50">
                    <span class="font-medium text-gray-800">HQ - Riyadh WH</span>
                    <span class="font-bold text-gray-900">105 Units</span>
                </li>
                <li class="p-4 flex justify-between items-center hover:bg-gray-50">
                    <span class="font-medium text-gray-800">Jeddah Distribution</span>
                    <span class="font-bold text-gray-900">40 Units</span>
                </li>
            </ul>
        </div>
        
        <div class="card p-5 bg-gray-50">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">Reorder Rules</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Min Qty (Reorder Point)</span>
                    <span class="font-bold text-red-600">50</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Max Qty (Target)</span>
                    <span class="font-bold text-gray-900">300</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Lead Time</span>
                    <span class="font-bold text-gray-900">14 Days</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Item Ledger (Movements) -->
    <div class="lg:col-span-2 card p-0 flex flex-col h-[500px]">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center shadow-sm z-10">
            <h3 class="font-bold text-gray-800"><i class="fas fa-history text-gray-400 mr-2"></i> Recent Stock Movements (Ledger)</h3>
            <button class="text-sm font-medium text-nour-primary hover:underline">View Full Ledger</button>
        </div>
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-white text-gray-500 text-[10px] uppercase tracking-wider border-b border-gray-200 sticky top-0 shadow-sm">
                    <tr>
                        <th class="p-3 font-semibold">Date</th>
                        <th class="p-3 font-semibold">Type</th>
                        <th class="p-3 font-semibold">Reference</th>
                        <th class="p-3 font-semibold text-right w-24">Qty</th>
                        <th class="p-3 font-semibold text-right w-24">Balance</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    <tr class="hover:bg-blue-50/20">
                        <td class="p-3 text-gray-500 text-xs">Aug 16, 14:30</td>
                        <td class="p-3"><span class="bg-red-100 text-red-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">OUT</span></td>
                        <td class="p-3 font-medium text-gray-800 text-xs">Delivery: DEL-2026-042</td>
                        <td class="p-3 text-right font-bold text-red-600">-5</td>
                        <td class="p-3 text-right font-black text-gray-900">145</td>
                    </tr>
                    <tr class="hover:bg-blue-50/20">
                        <td class="p-3 text-gray-500 text-xs">Aug 15, 09:15</td>
                        <td class="p-3"><span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">IN</span></td>
                        <td class="p-3 font-medium text-gray-800 text-xs">Receipt: GRN-2026-080</td>
                        <td class="p-3 text-right font-bold text-green-600">+100</td>
                        <td class="p-3 text-right font-black text-gray-900">150</td>
                    </tr>
                    <tr class="hover:bg-blue-50/20">
                        <td class="p-3 text-gray-500 text-xs">Aug 10, 11:00</td>
                        <td class="p-3"><span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">TRF</span></td>
                        <td class="p-3 font-medium text-gray-800 text-xs">Transfer to Jeddah</td>
                        <td class="p-3 text-right font-bold text-gray-700">-40</td>
                        <td class="p-3 text-right font-black text-gray-900">50</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>