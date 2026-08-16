<?php
// Path: resources/views/purchasing/requisitions/index.php
$pageTitle = 'Purchase Requisitions';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Purchase Requisitions (PR)</h1>
        <p class="text-sm text-gray-500 mt-1">Internal requests for materials or services awaiting procurement approval.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md flex items-center">
            <i class="fas fa-plus mr-2"></i> New Request
        </button>
    </div>
</div>

<div class="card p-0 overflow-visible relative">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex gap-4">
        <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600">
            <option>Pending Approval</option>
            <option>Approved</option>
            <option>All Requests</option>
        </select>
    </div>
    <div class="table-responsive rounded-b-lg">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">PR Number</th>
                    <th class="p-4 font-semibold">Department</th>
                    <th class="p-4 font-semibold">Requester</th>
                    <th class="p-4 font-semibold text-right">Est. Value</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-nour-primary">PR-2608-011</td>
                    <td class="p-4 text-gray-700 font-medium">Information Technology</td>
                    <td class="p-4 text-gray-600">Ahmed Hassan</td>
                    <td class="p-4 text-right font-bold text-gray-900">$4,500.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded text-[10px] uppercase font-bold">Pending</span></td>
                    <td class="p-4 text-right">
                        <button class="text-green-600 hover:text-green-800 font-bold text-xs mr-2">Approve</button>
                        <button class="text-gray-400 hover:text-blue-500 px-1"><i class="fas fa-eye"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors opacity-75">
                    <td class="p-4 font-bold text-gray-500">PR-2608-010</td>
                    <td class="p-4 text-gray-700 font-medium">Marketing</td>
                    <td class="p-4 text-gray-600">Sara Smith</td>
                    <td class="p-4 text-right font-bold text-gray-900">$1,200.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-[10px] uppercase font-bold">Approved</span></td>
                    <td class="p-4 text-right">
                        <span class="text-xs text-gray-400 font-medium"><i class="fas fa-check mr-1"></i> Done</span>
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