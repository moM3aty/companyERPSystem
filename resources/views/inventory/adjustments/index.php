<?php
// Path: resources/views/inventory/adjustments/index.php
$pageTitle = 'Stock Adjustments';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><a href="/inventory" class="hover:text-nour-primary transition-colors">Inventory</a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Adjustments</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Stock Adjustments</h1>
        <p class="text-sm text-gray-500 mt-1">Record physical inventory counts, shrinkage, damage, and initial stock balances.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="/inventory/adjustments/create" class="btn-primary shadow-md flex items-center">
            <i class="fas fa-sliders-h mr-2"></i> New Adjustment
        </a>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <div class="flex gap-2">
            <input type="text" placeholder="Search Ref or Reason..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-64">
        </div>
        <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600">
            <option>All Statuses</option>
            <option>Draft</option>
            <option>Posted</option>
        </select>
    </div>
    
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Reference No.</th>
                    <th class="p-4 font-semibold">Date</th>
                    <th class="p-4 font-semibold">Warehouse / Location</th>
                    <th class="p-4 font-semibold">Reason</th>
                    <th class="p-4 font-semibold text-center">Items Changed</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-nour-primary">ADJ-2026-0815</td>
                    <td class="p-4 text-gray-600">Aug 15, 2026</td>
                    <td class="p-4 font-medium text-gray-800">HQ - Main Riyadh</td>
                    <td class="p-4 text-gray-600">Annual Physical Count (Stock Take)</td>
                    <td class="p-4 text-center font-bold text-gray-700">45</td>
                    <td class="p-4 text-center"><span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-[10px] uppercase font-bold">Posted</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary px-2"><i class="fas fa-eye"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-gray-600">ADJ-2026-0816</td>
                    <td class="p-4 text-gray-600">Aug 16, 2026</td>
                    <td class="p-4 font-medium text-gray-800">Jeddah Distribution</td>
                    <td class="p-4 text-gray-600">Water Damage (Forklift Accident)</td>
                    <td class="p-4 text-center font-bold text-gray-700">2</td>
                    <td class="p-4 text-center"><span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-[10px] uppercase font-bold">Draft</span></td>
                    <td class="p-4 text-right">
                        <button class="text-green-500 hover:text-green-700 font-bold text-xs mr-2"><i class="fas fa-check mr-1"></i> Post</button>
                        <button class="text-gray-400 hover:text-blue-500 px-1"><i class="far fa-edit"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>