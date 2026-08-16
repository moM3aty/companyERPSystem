<?php
// Path: resources/views/enterprise/advanced-pricing/contracts.php
$pageTitle = 'Pricing Contracts';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Customer Pricing Contracts</h1>
        <p class="text-sm text-gray-500 mt-1">Manage long-term negotiated prices locked for specific B2B customers.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md">
            <i class="fas fa-file-contract mr-2"></i> New Contract
        </button>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex gap-4">
        <input type="text" placeholder="Search Customer or Contract No..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-64">
        <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600">
            <option>Active Contracts</option>
            <option>Expiring Soon (30d)</option>
            <option>Expired</option>
        </select>
    </div>
    
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Contract Ref.</th>
                    <th class="p-4 font-semibold">Customer (B2B)</th>
                    <th class="p-4 font-semibold">Valid Period</th>
                    <th class="p-4 font-semibold text-center">Items Locked</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100 text-gray-700">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-nour-primary">CTR-2026-001</td>
                    <td class="p-4 font-medium text-gray-900">Global Pharma Group</td>
                    <td class="p-4 text-gray-600">Jan 01, 2026 - Dec 31, 2026</td>
                    <td class="p-4 text-center font-bold text-gray-800">12</td>
                    <td class="p-4 text-center"><span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-green-200">Active</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary px-2"><i class="fas fa-eye"></i></button>
                        <button class="text-gray-400 hover:text-blue-500 px-2"><i class="far fa-edit"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-red-50/20 transition-colors bg-red-50/10">
                    <td class="p-4 font-bold text-nour-primary">CTR-2025-088</td>
                    <td class="p-4 font-medium text-gray-900">Alpha Tech Solutions</td>
                    <td class="p-4 text-gray-600">Sep 01, 2025 - <span class="text-red-600 font-bold">Aug 31, 2026</span></td>
                    <td class="p-4 text-center font-bold text-gray-800">45</td>
                    <td class="p-4 text-center">
                        <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-red-200 flex items-center justify-center gap-1 w-max mx-auto">
                            <i class="fas fa-exclamation-circle"></i> Expiring
                        </span>
                    </td>
                    <td class="p-4 text-right">
                        <button class="text-xs bg-white border border-gray-300 px-2 py-1 rounded hover:bg-gray-50 font-medium">Renew</button>
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