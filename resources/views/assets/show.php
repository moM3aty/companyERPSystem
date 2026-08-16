<?php
// Path: resources/views/assets/show.php
$pageTitle = 'Asset Details: AST-VEH-0012';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><a href="/assets" class="hover:text-nour-primary">Assets</a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">AST-VEH-0012</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Delivery Truck - Isuzu 2024</h1>
        <p class="text-sm text-gray-500 mt-1 font-mono">Code: AST-VEH-0012 | Category: Vehicles</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50"><i class="fas fa-barcode mr-1"></i> Print Tag</button>
        <button class="bg-red-50 text-red-600 border border-red-200 px-4 py-2 rounded-lg shadow-sm text-sm font-medium hover:bg-red-100">Dispose Asset</button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card p-5 border-l-4 border-l-blue-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Gross Purchase Value</p>
        <h3 class="text-3xl font-black text-gray-900">$45,000.00</h3>
        <p class="text-xs text-gray-400 mt-1">Purchased: Jan 01, 2024</p>
    </div>
    <div class="card p-5 border-l-4 border-l-red-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Accumulated Depreciation</p>
        <h3 class="text-3xl font-black text-red-600">$9,000.00</h3>
        <p class="text-xs text-gray-400 mt-1">Straight Line (5 Years)</p>
    </div>
    <div class="card p-5 border-l-4 border-l-green-500 bg-green-50/30">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Net Book Value (NBV)</p>
        <h3 class="text-3xl font-black text-green-700">$36,000.00</h3>
        <p class="text-xs text-green-600 mt-1 font-bold">As of Aug 2026</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Col: Details -->
    <div class="lg:col-span-1 space-y-6">
        <div class="card p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2 uppercase tracking-wider">Asset Information</h3>
            <div class="space-y-3 text-sm">
                <div class="flex flex-col"><span class="text-gray-500 text-xs">Status</span><span class="font-bold text-green-600">Active / In Use</span></div>
                <div class="flex flex-col"><span class="text-gray-500 text-xs">Assigned Location</span><span class="font-medium text-gray-900">HQ - Riyadh Transport Hub</span></div>
                <div class="flex flex-col"><span class="text-gray-500 text-xs">Serial / VIN Number</span><span class="font-mono text-gray-700">ISZM9988223344</span></div>
                <div class="flex flex-col"><span class="text-gray-500 text-xs">Salvage Value</span><span class="font-medium text-gray-900">$5,000.00</span></div>
            </div>
            
            <div class="mt-6 text-center">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=AST-VEH-0012" alt="Asset QR" class="mx-auto border p-2 rounded bg-white">
                <p class="text-[10px] text-gray-400 mt-2 font-mono">Scan for Asset Audit</p>
            </div>
        </div>
    </div>

    <!-- Right Col: Depreciation Schedule -->
    <div class="lg:col-span-2">
        <div class="card p-0 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800"><i class="fas fa-chart-line text-nour-primary mr-2"></i> Depreciation Schedule (Projected)</h3>
                <span class="text-xs text-gray-500">Method: Straight Line</span>
            </div>
            <div class="table-responsive">
                <table class="w-full text-left border-collapse whitespace-nowrap text-sm">
                    <thead>
                        <tr class="bg-white text-gray-500 text-xs uppercase border-b border-gray-200">
                            <th class="p-4 font-semibold text-center">Year</th>
                            <th class="p-4 font-semibold text-right">Depreciation Expense</th>
                            <th class="p-4 font-semibold text-right">Accumulated Depr.</th>
                            <th class="p-4 font-semibold text-right">Net Book Value</th>
                            <th class="p-4 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="bg-gray-50 text-gray-500">
                            <td class="p-4 text-center">0 (Purchase)</td>
                            <td class="p-4 text-right">-</td>
                            <td class="p-4 text-right">$0.00</td>
                            <td class="p-4 text-right font-bold">$45,000.00</td>
                            <td class="p-4 text-center"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr class="bg-gray-50 text-gray-500">
                            <td class="p-4 text-center">1 (2024)</td>
                            <td class="p-4 text-right">$8,000.00</td>
                            <td class="p-4 text-right">$8,000.00</td>
                            <td class="p-4 text-right font-bold">$37,000.00</td>
                            <td class="p-4 text-center"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr class="bg-blue-50/20">
                            <td class="p-4 text-center font-bold text-nour-primary">2 (2025)</td>
                            <td class="p-4 text-right font-medium">$8,000.00</td>
                            <td class="p-4 text-right font-medium">$16,000.00</td>
                            <td class="p-4 text-right font-bold text-gray-900">$29,000.00</td>
                            <td class="p-4 text-center"><span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold">Current</span></td>
                        </tr>
                        <tr class="hover:bg-gray-50 opacity-60">
                            <td class="p-4 text-center">3 (2026)</td>
                            <td class="p-4 text-right">$8,000.00</td>
                            <td class="p-4 text-right">$24,000.00</td>
                            <td class="p-4 text-right font-bold">$21,000.00</td>
                            <td class="p-4 text-center">Projected</td>
                        </tr>
                        <tr class="hover:bg-gray-50 opacity-60">
                            <td class="p-4 text-center">4 (2027)</td>
                            <td class="p-4 text-right">$8,000.00</td>
                            <td class="p-4 text-right">$32,000.00</td>
                            <td class="p-4 text-right font-bold">$13,000.00</td>
                            <td class="p-4 text-center">Projected</td>
                        </tr>
                        <tr class="hover:bg-gray-50 opacity-60">
                            <td class="p-4 text-center">5 (2028)</td>
                            <td class="p-4 text-right">$8,000.00</td>
                            <td class="p-4 text-right">$40,000.00</td>
                            <td class="p-4 text-right font-bold text-red-500">$5,000.00 (Salvage)</td>
                            <td class="p-4 text-center">Projected</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>