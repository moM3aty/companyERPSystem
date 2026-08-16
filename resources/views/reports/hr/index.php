<?php
// Path: resources/views/reports/hr/index.php
$pageTitle = 'HR Analytics';
ob_start();
?>
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/reports" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Report Center</a>
        <h1 class="text-2xl font-bold text-gray-800">Human Resources Analytics</h1>
        <p class="text-sm text-gray-500 mt-1">Track headcount, turnover rates, and departmental distribution.</p>
    </div>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="card p-5 border-t-4 border-t-nour-primary">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Headcount</p>
        <h3 class="text-2xl font-black text-gray-900">142</h3>
        <p class="text-[10px] text-green-500 mt-1 font-bold">+5 this quarter</p>
    </div>
    <div class="card p-5 border-t-4 border-t-red-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Turnover Rate (YTD)</p>
        <h3 class="text-2xl font-black text-red-600">4.2%</h3>
        <p class="text-[10px] text-gray-400 mt-1">Industry avg: 5.5%</p>
    </div>
    <div class="card p-5 border-t-4 border-t-yellow-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Avg. Absence Rate</p>
        <h3 class="text-2xl font-black text-gray-900">2.1%</h3>
    </div>
    <div class="card p-5 border-t-4 border-t-green-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Avg. Tenure</p>
        <h3 class="text-2xl font-black text-gray-900">4.5 Yrs</h3>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="card p-5 h-80 flex flex-col">
        <h3 class="font-bold text-gray-800 mb-2 border-b border-gray-100 pb-2">Headcount by Department</h3>
        <div class="flex-1 bg-gray-50 border border-dashed border-gray-200 rounded flex items-center justify-center text-gray-400">
            <i class="fas fa-chart-bar text-4xl mb-2 opacity-50"></i><br>Chart.js Bar Chart Here
        </div>
    </div>
    
    <div class="card p-0 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50"><h3 class="font-bold text-gray-800">Recent Terminations / Resignations</h3></div>
        <div class="table-responsive">
            <table class="w-full text-left border-collapse whitespace-nowrap text-sm">
                <thead>
                    <tr class="bg-white text-gray-500 text-xs uppercase border-b border-gray-200">
                        <th class="p-3">Employee</th>
                        <th class="p-3">Department</th>
                        <th class="p-3">Exit Date</th>
                        <th class="p-3 text-center">Type</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-gray-900">John Doe</td>
                        <td class="p-3 text-gray-600">Sales</td>
                        <td class="p-3 text-gray-600">Jul 30, 2026</td>
                        <td class="p-3 text-center"><span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Resigned</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>