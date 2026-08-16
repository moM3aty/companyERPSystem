<?php
// Path: resources/views/maintenance/work-orders/index.php
$pageTitle = 'Maintenance Work Orders';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Maintenance Work Orders</h1>
        <p class="text-sm text-gray-500 mt-1">Manage corrective and preventive maintenance tasks and spare parts.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-filter mr-1"></i> Filter
        </button>
        <button class="btn-primary shadow-md">
            <i class="fas fa-plus mr-2"></i> Create WO
        </button>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex gap-4">
        <div class="relative flex-1 max-w-md">
            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
            <input type="text" placeholder="Search WO number or asset..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary text-sm bg-white">
        </div>
    </div>
    
    <div class="table-responsive rounded-b-lg">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">WO Number</th>
                    <th class="p-4 font-semibold">Asset / Description</th>
                    <th class="p-4 font-semibold">Assigned Tech</th>
                    <th class="p-4 font-semibold text-center">Priority</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 font-bold text-nour-primary">WO-2608-015</td>
                    <td class="p-4">
                        <span class="font-bold text-gray-900 block">CNC Machine Alpha</span>
                        <span class="text-xs text-gray-500 truncate w-48 block">Spindle calibration and lubrication.</span>
                    </td>
                    <td class="p-4 font-medium text-gray-700">Ali Omar</td>
                    <td class="p-4 text-center"><span class="text-red-600 font-bold text-xs"><i class="fas fa-exclamation-circle mr-1"></i> Critical</span></td>
                    <td class="p-4 text-center"><span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-blue-200">In Progress</span></td>
                    <td class="p-4 text-right">
                        <button class="bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1 rounded text-xs font-bold transition-colors">Complete</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 font-bold text-gray-600">WO-2608-012</td>
                    <td class="p-4">
                        <span class="font-bold text-gray-900 block">Forklift T-3</span>
                        <span class="text-xs text-gray-500 truncate w-48 block">Routine oil change (PM).</span>
                    </td>
                    <td class="p-4 font-medium text-gray-700">Vendor: SafeLift</td>
                    <td class="p-4 text-center"><span class="text-gray-500 font-bold text-xs">Normal</span></td>
                    <td class="p-4 text-center"><span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-gray-200">Completed</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary px-2" title="View Details"><i class="fas fa-eye"></i></button>
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