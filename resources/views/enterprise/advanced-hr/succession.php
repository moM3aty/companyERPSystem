<?php
// Path: resources/views/enterprise/advanced-hr/succession.php
$pageTitle = 'Succession Planning';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Succession Planning</h1>
        <p class="text-sm text-gray-500 mt-1">Identify and develop internal personnel to fill key leadership positions.</p>
    </div>
</div>

<div class="card p-0 overflow-hidden z-10 relative">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Critical Roles Matrix</h3>
        <button class="bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded text-sm font-medium hover:bg-gray-100 shadow-sm">
            <i class="fas fa-filter mr-1"></i> Filter by Dept
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Key Role</th>
                    <th class="p-4 font-semibold">Current Incumbent</th>
                    <th class="p-4 font-semibold text-center">Flight/Retire Risk</th>
                    <th class="p-4 font-semibold">Ready Now (1-2 Yrs)</th>
                    <th class="p-4 font-semibold">Ready Later (3-5 Yrs)</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-gray-900">Chief Financial Officer (CFO)</td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Sarah+Smith&background=0a1930&color=fff" class="w-6 h-6 rounded-full">
                            <span class="font-medium text-gray-800">Sarah Smith</span>
                        </div>
                    </td>
                    <td class="p-4 text-center"><span class="bg-red-100 text-red-800 px-2 py-0.5 rounded text-[10px] uppercase font-bold">High (Retiring 2027)</span></td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Ali+Omar&background=005eb8&color=fff" class="w-6 h-6 rounded-full border border-green-500">
                            <span class="text-xs font-medium text-gray-700">Ali Omar (VP Finance)</span>
                        </div>
                    </td>
                    <td class="p-4 text-gray-400 text-xs italic">No candidates identified</td>
                    <td class="p-4 text-right">
                        <button class="text-nour-primary hover:underline text-xs font-medium">Edit Plan</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-gray-900">Lead ERP Architect</td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Ahmed+Hassan&background=f59e0b&color=fff" class="w-6 h-6 rounded-full">
                            <span class="font-medium text-gray-800">Ahmed Hassan</span>
                        </div>
                    </td>
                    <td class="p-4 text-center"><span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-[10px] uppercase font-bold">Medium Risk</span></td>
                    <td class="p-4 text-red-500 text-xs font-bold"><i class="fas fa-exclamation-circle mr-1"></i> Critical Gap</td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Zaid+M&background=21a1f1&color=fff" class="w-6 h-6 rounded-full border border-yellow-500">
                            <span class="text-xs font-medium text-gray-700">Zaid M. (Senior Dev)</span>
                        </div>
                    </td>
                    <td class="p-4 text-right">
                        <button class="text-nour-primary hover:underline text-xs font-medium">Edit Plan</button>
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