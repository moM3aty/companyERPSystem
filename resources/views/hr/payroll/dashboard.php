<?php
// Path: resources/views/hr/payroll/dashboard.php
$pageTitle = 'Payroll Processing Center';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i> Home</a>
                </li>
                <li><i class="fas fa-chevron-right text-xs mx-2"></i><span>HR</span></li>
                <li><i class="fas fa-chevron-right text-xs mx-2"></i><span class="text-gray-800 font-medium">Payroll</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Payroll Run: <span class="text-nour-primary">August 2026</span></h1>
        <p class="text-sm text-gray-500 mt-1">Review timesheets, calculate salaries, and generate payslips.</p>
    </div>
    
    <div class="mt-4 sm:mt-0 flex gap-3">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors flex items-center">
            <i class="fas fa-file-excel mr-2 text-green-600"></i> Export Bank File
        </button>
        <button class="btn-primary flex items-center shadow-md shadow-nour-primary/30" onclick="confirm('Are you sure you want to finalize payroll for August 2026? This action will generate journal entries.')">
            <i class="fas fa-check-double mr-2"></i> Finalize Payroll
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Total Net Salary -->
    <div class="card p-6 border-t-4 border-t-blue-500 relative overflow-hidden">
        <div class="absolute right-0 top-0 mt-4 mr-4 text-blue-100"><i class="fas fa-money-bill-wave text-5xl"></i></div>
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 relative z-10">Total Net Payout</p>
        <h3 class="text-3xl font-bold text-gray-900 relative z-10">SAR 1,245,600</h3>
        <p class="text-sm text-gray-500 mt-2 relative z-10">For 142 active employees</p>
    </div>

    <!-- Deductions -->
    <div class="card p-6 border-t-4 border-t-red-500 relative overflow-hidden">
        <div class="absolute right-0 top-0 mt-4 mr-4 text-red-100"><i class="fas fa-hand-holding-usd text-5xl"></i></div>
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 relative z-10">Total Deductions (GOSI, Absences)</p>
        <h3 class="text-3xl font-bold text-gray-900 relative z-10">SAR 112,450</h3>
        <p class="text-sm text-gray-500 mt-2 relative z-10">Includes social insurance & penalties</p>
    </div>

    <!-- Bonuses / Allowances -->
    <div class="card p-6 border-t-4 border-t-green-500 relative overflow-hidden">
        <div class="absolute right-0 top-0 mt-4 mr-4 text-green-100"><i class="fas fa-award text-5xl"></i></div>
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 relative z-10">Bonuses & Overtime</p>
        <h3 class="text-3xl font-bold text-gray-900 relative z-10">SAR 85,200</h3>
        <p class="text-sm text-gray-500 mt-2 relative z-10">Sales commissions and approved OT</p>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <h3 class="text-base font-bold text-gray-800">Payslips Review (Draft)</h3>
        <div class="flex gap-2">
            <input type="text" placeholder="Search employee..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary">
            <button class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded-md text-sm font-medium hover:bg-gray-300 transition-colors">
                <i class="fas fa-calculator mr-1"></i> Recalculate All
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Employee</th>
                    <th class="p-4 font-semibold text-right">Basic Salary</th>
                    <th class="p-4 font-semibold text-right text-green-600">Allowances</th>
                    <th class="p-4 font-semibold text-right text-red-600">Deductions</th>
                    <th class="p-4 font-semibold text-right text-gray-900">Net Pay</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4">
                        <div class="font-bold text-gray-900">Ahmed Hassan</div>
                        <div class="text-xs text-gray-500">EMP-2022-045 &bull; IT Dept</div>
                    </td>
                    <td class="p-4 text-right font-medium">SAR 15,000.00</td>
                    <td class="p-4 text-right text-green-600">+ SAR 3,750.00 <span class="block text-[10px] text-gray-400">Housing, Trans</span></td>
                    <td class="p-4 text-right text-red-600">- SAR 1,450.00 <span class="block text-[10px] text-gray-400">GOSI (9.75%)</span></td>
                    <td class="p-4 text-right font-bold text-gray-900 text-base">SAR 17,300.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-1 bg-blue-100 text-blue-800 text-[10px] uppercase font-bold rounded">Draft</span></td>
                    <td class="p-4 text-right">
                        <button class="text-nour-primary hover:text-nour-dark text-xs font-medium border border-nour-primary/30 px-2 py-1 rounded bg-nour-primary/5 transition-colors">View Details</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4">
                        <div class="font-bold text-gray-900">Omar Khalid</div>
                        <div class="text-xs text-gray-500">EMP-2024-005 &bull; Sales</div>
                    </td>
                    <td class="p-4 text-right font-medium">SAR 10,000.00</td>
                    <td class="p-4 text-right text-green-600">+ SAR 8,500.00 <span class="block text-[10px] text-gray-400">Comm, Trans, Housing</span></td>
                    <td class="p-4 text-right text-red-600">- SAR 975.00 <span class="block text-[10px] text-gray-400">GOSI</span></td>
                    <td class="p-4 text-right font-bold text-gray-900 text-base">SAR 17,525.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-[10px] uppercase font-bold rounded">Needs Review</span></td>
                    <td class="p-4 text-right">
                        <button class="text-nour-primary hover:text-nour-dark text-xs font-medium border border-nour-primary/30 px-2 py-1 rounded bg-nour-primary/5 transition-colors">View Details</button>
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