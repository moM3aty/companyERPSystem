<?php
// Path: resources/views/projects/timesheets/index.php
$pageTitle = 'Project Timesheets';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Project Timesheets</h1>
        <p class="text-sm text-gray-500 mt-1">Log worked hours against specific project tasks for accurate labor costing and billing.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-check-double mr-2"></i> Approve All
        </button>
        <button class="btn-primary shadow-md">
            <i class="fas fa-clock mr-2"></i> Log Time
        </button>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex gap-4">
        <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600">
            <option>All Projects</option>
            <option>ERP Implementation Phase 2</option>
            <option>Network Upgrade Infrastructure</option>
        </select>
        <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600">
            <option>Pending Approval</option>
            <option>Approved / Unbilled</option>
            <option>Billed</option>
        </select>
    </div>
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Date</th>
                    <th class="p-4 font-semibold">Employee</th>
                    <th class="p-4 font-semibold">Project / Task</th>
                    <th class="p-4 font-semibold text-center">Hours</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-medium text-gray-800">Aug 16, 2026</td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Ahmed+Hassan&background=005eb8&color=fff" class="w-6 h-6 rounded-full">
                            <span class="font-medium text-gray-700">Ahmed Hassan</span>
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="block font-bold text-gray-900">ERP Implementation Phase 2</span>
                        <span class="block text-xs text-gray-500">Configure ZATCA Endpoints</span>
                    </td>
                    <td class="p-4 text-center font-black text-nour-primary text-base">4.5</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded text-[10px] uppercase font-bold">Pending</span></td>
                    <td class="p-4 text-right">
                        <button class="text-green-600 hover:text-green-800 px-2 font-bold text-xs"><i class="fas fa-check mr-1"></i> Approve</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-medium text-gray-800">Aug 15, 2026</td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Sarah+Smith&background=f59e0b&color=fff" class="w-6 h-6 rounded-full">
                            <span class="font-medium text-gray-700">Sarah Smith</span>
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="block font-bold text-gray-900">Network Upgrade Infrastructure</span>
                        <span class="block text-xs text-gray-500">Server Rack Installation</span>
                    </td>
                    <td class="p-4 text-center font-black text-nour-primary text-base">8.0</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-[10px] uppercase font-bold">Approved</span></td>
                    <td class="p-4 text-right">
                        <span class="text-[10px] text-gray-400 font-medium">Unbilled</span>
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