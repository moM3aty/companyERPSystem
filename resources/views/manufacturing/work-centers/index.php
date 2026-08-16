<?php
// Path: resources/views/manufacturing/work-centers/index.php
$pageTitle = 'Work Centers';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Work Centers</h1>
        <p class="text-sm text-gray-500 mt-1">Manage physical production lines, machines, and calculate operating costs per hour.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md flex items-center">
            <i class="fas fa-plus mr-2"></i> Add Work Center
        </button>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Code</th>
                    <th class="p-4 font-semibold">Work Center Name</th>
                    <th class="p-4 font-semibold text-center">Type</th>
                    <th class="p-4 font-semibold text-right">Cost / Hour</th>
                    <th class="p-4 font-semibold text-center">Daily Capacity (Hrs)</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 font-mono text-xs text-gray-500">WC-ASSM-01</td>
                    <td class="p-4 font-bold text-gray-900">Assembly Line 1 (Electronics)</td>
                    <td class="p-4 text-center"><span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Human/Mixed</span></td>
                    <td class="p-4 text-right font-bold text-gray-700">$45.00</td>
                    <td class="p-4 text-center font-medium text-gray-800">16</td>
                    <td class="p-4 text-center"><span class="text-green-500"><i class="fas fa-check-circle"></i> Active</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-blue-500 px-2"><i class="far fa-edit"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 font-mono text-xs text-gray-500">WC-CNC-01</td>
                    <td class="p-4 font-bold text-gray-900">Heavy CNC Machine Alpha</td>
                    <td class="p-4 text-center"><span class="bg-purple-100 text-purple-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Machine</span></td>
                    <td class="p-4 text-right font-bold text-gray-700">$120.00</td>
                    <td class="p-4 text-center font-medium text-gray-800">22</td>
                    <td class="p-4 text-center"><span class="text-red-500"><i class="fas fa-tools"></i> Maintenance</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-blue-500 px-2"><i class="far fa-edit"></i></button>
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