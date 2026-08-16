<?php
// Path: resources/views/reports/exports.php
$pageTitle = 'Data Exports Log';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/reports" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Report Center</a>
        <h1 class="text-2xl font-bold text-gray-800">Export History</h1>
        <p class="text-sm text-gray-500 mt-1">Download previously generated reports and data exports.</p>
    </div>
</div>

<div class="card p-0 overflow-hidden max-w-5xl">
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Report Name</th>
                    <th class="p-4 font-semibold">Generated On</th>
                    <th class="p-4 font-semibold">Format</th>
                    <th class="p-4 font-semibold">Size</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-medium text-gray-900">Inventory Valuation Snapshot (Aug 2026)</td>
                    <td class="p-4 text-gray-600">Aug 15, 2026 14:00</td>
                    <td class="p-4"><span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase"><i class="fas fa-file-excel mr-1"></i> XLSX</span></td>
                    <td class="p-4 text-gray-500 text-xs">2.4 MB</td>
                    <td class="p-4 text-center"><span class="text-green-500 font-medium text-xs"><i class="fas fa-check-circle mr-1"></i> Ready</span></td>
                    <td class="p-4 text-right">
                        <button class="text-nour-primary hover:underline font-medium text-sm">Download</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-medium text-gray-900">Q3 Tax Return Summary</td>
                    <td class="p-4 text-gray-600">Aug 14, 2026 09:30</td>
                    <td class="p-4"><span class="bg-red-100 text-red-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase"><i class="fas fa-file-pdf mr-1"></i> PDF</span></td>
                    <td class="p-4 text-gray-500 text-xs">845 KB</td>
                    <td class="p-4 text-center"><span class="text-green-500 font-medium text-xs"><i class="fas fa-check-circle mr-1"></i> Ready</span></td>
                    <td class="p-4 text-right">
                        <button class="text-nour-primary hover:underline font-medium text-sm">Download</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors opacity-60">
                    <td class="p-4 font-medium text-gray-900">Customer List Backup</td>
                    <td class="p-4 text-gray-600">Jul 01, 2026 11:00</td>
                    <td class="p-4"><span class="bg-gray-200 text-gray-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase"><i class="fas fa-file-csv mr-1"></i> CSV</span></td>
                    <td class="p-4 text-gray-500 text-xs">-</td>
                    <td class="p-4 text-center"><span class="text-gray-400 font-medium text-xs"><i class="fas fa-clock mr-1"></i> Expired (Deleted)</span></td>
                    <td class="p-4 text-right">
                        <span class="text-gray-400 text-xs">Unavailable</span>
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