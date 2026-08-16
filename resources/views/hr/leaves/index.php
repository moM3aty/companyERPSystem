<?php
// Path: resources/views/hr/leaves/index.php
$pageTitle = 'Leave Requests';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Leave Requests</h1>
        <p class="text-sm text-gray-500 mt-1">Review and approve employee vacation and sick leave requests.</p>
    </div>
</div>

<div class="card p-0 overflow-visible relative">
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Employee</th>
                    <th class="p-4 font-semibold">Leave Type</th>
                    <th class="p-4 font-semibold text-center">Dates (Duration)</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-gray-900">Sara Ali</td>
                    <td class="p-4"><span class="text-xs font-bold text-blue-600">Annual Leave</span></td>
                    <td class="p-4 text-center text-gray-700">Aug 20 - Aug 25 <span class="font-bold text-nour-primary ml-1">(5 Days)</span></td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded text-[10px] uppercase font-bold">Pending</span></td>
                    <td class="p-4 text-right">
                        <button class="bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1 rounded text-xs font-bold mr-1 transition-colors">Approve</button>
                        <button class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded text-xs font-bold transition-colors">Reject</button>
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