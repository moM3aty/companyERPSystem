<?php
// Path: resources/views/reports/scheduled.php
$pageTitle = 'Scheduled Reports';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/reports" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Report Center</a>
        <h1 class="text-2xl font-bold text-gray-800">Scheduled Report Jobs (Cron)</h1>
        <p class="text-sm text-gray-500 mt-1">Manage automated report generation and email deliveries.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md flex items-center">
            <i class="fas fa-clock mr-2"></i> New Schedule
        </button>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Report Name</th>
                    <th class="p-4 font-semibold">Frequency</th>
                    <th class="p-4 font-semibold">Recipients</th>
                    <th class="p-4 font-semibold">Next Run</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-nour-dark">Daily Sales Summary</td>
                    <td class="p-4"><span class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded text-xs font-mono">Daily @ 18:00</span></td>
                    <td class="p-4 text-xs text-gray-500">management@nourtrust.com</td>
                    <td class="p-4 text-nour-primary font-medium">Today, 18:00</td>
                    <td class="p-4 text-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                        </label>
                    </td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-blue-500 px-1"><i class="far fa-edit"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-nour-dark">Monthly Inventory Valuation</td>
                    <td class="p-4"><span class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded text-xs font-mono">1st of Month @ 00:01</span></td>
                    <td class="p-4 text-xs text-gray-500">finance@nourtrust.com</td>
                    <td class="p-4 text-gray-600 font-medium">Sep 01, 2026</td>
                    <td class="p-4 text-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                        </label>
                    </td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-blue-500 px-1"><i class="far fa-edit"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>