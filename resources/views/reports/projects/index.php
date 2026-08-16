<?php
// Path: resources/views/reports/projects/index.php
$pageTitle = 'Projects Analytics';
ob_start();
?>
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/reports" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Report Center</a>
        <h1 class="text-2xl font-bold text-gray-800">PMO & Projects Analytics</h1>
        <p class="text-sm text-gray-500 mt-1">Track project profitability, budget vs actuals, and resource utilization.</p>
    </div>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="card p-5 border-t-4 border-t-nour-primary">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Active Budget</p>
        <h3 class="text-2xl font-black text-gray-900">$850,000</h3>
    </div>
    <div class="card p-5 border-t-4 border-t-red-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Budget Overruns</p>
        <h3 class="text-2xl font-black text-red-600">2 Projects</h3>
        <p class="text-[10px] text-gray-400 mt-1">Exceeded planned budget</p>
    </div>
    <div class="card p-5 border-t-4 border-t-green-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Billed (YTD)</p>
        <h3 class="text-2xl font-black text-green-600">$420,000</h3>
    </div>
    <div class="card p-5 border-t-4 border-t-blue-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Billable Hours Logged</p>
        <h3 class="text-2xl font-black text-blue-600">1,245 Hrs</h3>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="p-4 border-b border-gray-100 bg-gray-50"><h3 class="font-bold text-gray-800">Budget vs Actuals (Active Projects)</h3></div>
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap text-sm">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase border-b border-gray-200">
                    <th class="p-4">Project Name</th>
                    <th class="p-4 text-right">Planned Budget</th>
                    <th class="p-4 text-right text-red-600">Actual Cost (Labor + Exp)</th>
                    <th class="p-4 text-right text-green-600">Billed to Client</th>
                    <th class="p-4 text-center w-48">Budget Consumption</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-bold text-nour-dark">ERP Implementation Phase 2</td>
                    <td class="p-4 text-right font-medium">$45,000</td>
                    <td class="p-4 text-right font-bold text-gray-900">$22,500</td>
                    <td class="p-4 text-right font-bold text-green-600">$15,000</td>
                    <td class="p-4">
                        <div class="flex justify-between text-[10px] font-bold text-gray-500 mb-1"><span>50%</span></div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5"><div class="bg-blue-500 h-1.5 rounded-full" style="width: 50%"></div></div>
                    </td>
                </tr>
                <tr class="hover:bg-red-50/20 bg-red-50/10">
                    <td class="p-4 font-bold text-nour-dark">Network Upgrade Infrastructure</td>
                    <td class="p-4 text-right font-medium">$12,000</td>
                    <td class="p-4 text-right font-bold text-red-600">$14,500</td>
                    <td class="p-4 text-right font-bold text-green-600">$0</td>
                    <td class="p-4">
                        <div class="flex justify-between text-[10px] font-bold text-red-600 mb-1"><span>120% (Overrun)</span></div>
                        <div class="w-full bg-red-200 rounded-full h-1.5"><div class="bg-red-600 h-1.5 rounded-full" style="width: 100%"></div></div>
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