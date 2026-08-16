<?php
// Path: resources/views/reports/payroll/index.php
$pageTitle = 'Payroll Analytics';
ob_start();
?>
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/reports" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Report Center</a>
        <h1 class="text-2xl font-bold text-gray-800">Payroll & Compensation Analytics</h1>
        <p class="text-sm text-gray-500 mt-1">Analyze salary trends, overtime costs, and tax liabilities.</p>
    </div>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card p-5 border-t-4 border-t-nour-dark">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Payroll Expense (YTD)</p>
        <h3 class="text-3xl font-black text-gray-900">$2,450,000</h3>
        <p class="text-[10px] text-gray-400 mt-1 font-medium">Gross Salaries + Employer Contributions</p>
    </div>
    <div class="card p-5 border-t-4 border-t-purple-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Avg. Salary per Employee</p>
        <h3 class="text-3xl font-black text-purple-600">$4,250</h3>
    </div>
    <div class="card p-5 border-t-4 border-t-red-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Overtime Paid (YTD)</p>
        <h3 class="text-3xl font-black text-red-600">$85,200</h3>
        <p class="text-[10px] text-red-400 mt-1 font-bold"><i class="fas fa-exclamation-triangle"></i> High OT Alert</p>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="p-4 border-b border-gray-100 bg-gray-50"><h3 class="font-bold text-gray-800">Departmental Payroll Breakdown (Last Month)</h3></div>
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap text-sm">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase border-b border-gray-200">
                    <th class="p-4">Department</th>
                    <th class="p-4 text-center">Headcount</th>
                    <th class="p-4 text-right">Basic Salaries</th>
                    <th class="p-4 text-right">Allowances/OT</th>
                    <th class="p-4 text-right">Deductions</th>
                    <th class="p-4 text-right font-bold text-nour-primary">Net Payout</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-bold text-gray-900">Information Technology</td>
                    <td class="p-4 text-center font-medium">24</td>
                    <td class="p-4 text-right text-gray-600">$120,000</td>
                    <td class="p-4 text-right text-green-600">+$15,000</td>
                    <td class="p-4 text-right text-red-600">-$12,500</td>
                    <td class="p-4 text-right font-black text-gray-900">$122,500</td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-bold text-gray-900">Sales & Marketing</td>
                    <td class="p-4 text-center font-medium">45</td>
                    <td class="p-4 text-right text-gray-600">$180,000</td>
                    <td class="p-4 text-right text-green-600">+$45,000 <span class="text-[9px] text-gray-400 block">Commissions</span></td>
                    <td class="p-4 text-right text-red-600">-$18,000</td>
                    <td class="p-4 text-right font-black text-gray-900">$207,000</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>