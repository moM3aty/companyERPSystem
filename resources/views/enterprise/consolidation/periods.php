<?php
// Path: resources/views/enterprise/consolidation/periods.php
$pageTitle = 'Consolidation Periods';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Consolidation Periods Mapping</h1>
        <p class="text-sm text-gray-500 mt-1">Map and monitor the closing status of local periods across all subsidiaries.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md">
            <i class="fas fa-calendar-plus mr-2"></i> Open Group Period
        </button>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <h3 class="text-base font-bold text-gray-800">Q3 - August 2026 (Group Level)</h3>
        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">In Progress</span>
    </div>
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Subsidiary Entity</th>
                    <th class="p-4 font-semibold">Local Period</th>
                    <th class="p-4 font-semibold">Base Currency</th>
                    <th class="p-4 font-semibold text-center">Closing Status</th>
                    <th class="p-4 font-semibold text-center">Data Sync</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-nour-dark"><i class="fas fa-building text-nour-primary mr-2"></i> Nour Enterprise KSA</td>
                    <td class="p-4">August 2026</td>
                    <td class="p-4">SAR <span class="text-[10px] text-gray-400 ml-1">(Rate: 0.266)</span></td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center text-green-600 font-bold text-xs"><i class="fas fa-lock mr-1"></i> Locked</span>
                    </td>
                    <td class="p-4 text-center">
                        <span class="text-green-500"><i class="fas fa-check-circle"></i> 100%</span>
                    </td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary px-2"><i class="fas fa-eye"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-nour-dark"><i class="fas fa-building text-nour-primary mr-2"></i> Nour Logistics UAE</td>
                    <td class="p-4">August 2026</td>
                    <td class="p-4">AED <span class="text-[10px] text-gray-400 ml-1">(Rate: 0.272)</span></td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center text-yellow-600 font-bold text-xs"><i class="fas fa-lock-open mr-1"></i> Open</span>
                    </td>
                    <td class="p-4 text-center">
                        <span class="text-yellow-500"><i class="fas fa-sync fa-spin"></i> Pending</span>
                    </td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary px-2"><i class="fas fa-eye"></i></button>
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