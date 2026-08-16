<?php
// Path: resources/views/shifts/index.php
$pageTitle = 'POS Shifts Management';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">POS Shifts</h1>
        <p class="text-sm text-gray-500 mt-1">Manage cashier sessions and Z-Reports.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="/shifts/open" class="btn-primary flex items-center">
            <i class="fas fa-lock-open mr-2"></i> Open New Shift
        </a>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Cashier</th>
                    <th class="p-4 font-semibold">Terminal</th>
                    <th class="p-4 font-semibold">Opened At</th>
                    <th class="p-4 font-semibold">Closed At</th>
                    <th class="p-4 font-semibold text-right">Expected</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                <tr class="hover:bg-blue-50/30">
                    <td class="p-4 font-medium flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-nour-primary text-white flex items-center justify-center text-xs">AH</div>
                        Ahmed Hassan
                    </td>
                    <td class="p-4">TERM-01 (Main)</td>
                    <td class="p-4">Today, 08:00 AM</td>
                    <td class="p-4 text-gray-400">-</td>
                    <td class="p-4 text-right font-bold">$1,250.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">Open</span></td>
                    <td class="p-4 text-right">
                        <button class="text-red-500 hover:text-red-700 font-medium text-xs px-2 py-1 border border-red-200 rounded">Close Shift</button>
                    </td>
                </tr>
                <tr class="opacity-75 hover:bg-gray-50">
                    <td class="p-4 font-medium flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-gray-400 text-white flex items-center justify-center text-xs">SM</div>
                        Sarah Mohamed
                    </td>
                    <td class="p-4">TERM-02</td>
                    <td class="p-4">Yesterday, 04:00 PM</td>
                    <td class="p-4">Yesterday, 11:30 PM</td>
                    <td class="p-4 text-right font-medium">$3,420.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-1 bg-gray-200 text-gray-600 text-xs rounded-full font-medium">Closed</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-500 hover:text-nour-primary"><i class="fas fa-file-invoice-dollar"></i> Z-Report</button>
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