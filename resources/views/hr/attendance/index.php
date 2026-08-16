<?php
// Path: resources/views/hr/attendance/index.php
$pageTitle = 'Attendance Log';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Attendance Log</h1>
        <p class="text-sm text-gray-500 mt-1">Review daily employee check-ins, check-outs, and absence records.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50"><i class="fas fa-file-excel mr-1 text-green-600"></i> Export</button>
    </div>
</div>

<div class="card p-0 overflow-visible relative">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex gap-4 items-center justify-between">
        <input type="date" value="<?= date('Y-m-d') ?>" class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600 w-48">
        <span class="text-xs bg-red-100 text-red-800 px-3 py-1 rounded-full font-bold">3 Absent Today</span>
    </div>
    <div class="table-responsive rounded-b-lg">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Employee</th>
                    <th class="p-4 font-semibold text-center">Check In</th>
                    <th class="p-4 font-semibold text-center">Check Out</th>
                    <th class="p-4 font-semibold text-center">Late Minutes</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-gray-900">Ahmed Hassan <span class="block text-[10px] text-gray-400 font-normal">EMP-001</span></td>
                    <td class="p-4 text-center font-mono text-gray-700">08:05 AM</td>
                    <td class="p-4 text-center font-mono text-gray-700">-</td>
                    <td class="p-4 text-center text-red-500 font-bold">5</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-[10px] uppercase font-bold">Present</span></td>
                </tr>
                <tr class="hover:bg-red-50/20 transition-colors bg-red-50/10">
                    <td class="p-4 font-bold text-gray-900">Omar Khalid <span class="block text-[10px] text-gray-400 font-normal">EMP-012</span></td>
                    <td class="p-4 text-center text-gray-400">-</td>
                    <td class="p-4 text-center text-gray-400">-</td>
                    <td class="p-4 text-center text-gray-400">-</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-red-100 text-red-800 rounded text-[10px] uppercase font-bold">Absent</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>