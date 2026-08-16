<?php
// Path: resources/views/payroll/runs/index.php
$pageTitle = 'Payroll Batches';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><a href="/hr/payroll/dashboard" class="hover:text-nour-primary transition-colors">Payroll</a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Batches</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Payroll Runs & Batches</h1>
        <p class="text-sm text-gray-500 mt-1">Review historical payroll cycles, download bank files, and view journal entries.</p>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Period (Month)</th>
                    <th class="p-4 font-semibold">Reference No.</th>
                    <th class="p-4 font-semibold text-right">Gross Total</th>
                    <th class="p-4 font-semibold text-right text-red-500">Deductions</th>
                    <th class="p-4 font-semibold text-right text-nour-primary">Net Payout</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-blue-50/20 transition-colors group">
                    <td class="p-4 font-black text-gray-900 text-base">August 2026</td>
                    <td class="p-4 font-medium text-gray-600">PR-2026-08</td>
                    <td class="p-4 text-right font-medium text-gray-800">SAR 1,358,050.00</td>
                    <td class="p-4 text-right font-medium text-red-500">SAR 112,450.00</td>
                    <td class="p-4 text-right font-black text-nour-primary text-lg">SAR 1,245,600.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-[10px] uppercase font-bold">Draft / Review</span></td>
                    <td class="p-4 text-right">
                        <button class="bg-green-600 text-white hover:bg-green-700 px-3 py-1 rounded text-xs font-bold transition-colors shadow-sm">Post to GL</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors group opacity-80">
                    <td class="p-4 font-bold text-gray-800">July 2026</td>
                    <td class="p-4 font-medium text-gray-500">PR-2026-07</td>
                    <td class="p-4 text-right font-medium text-gray-600">SAR 1,320,000.00</td>
                    <td class="p-4 text-right font-medium text-gray-500">SAR 110,000.00</td>
                    <td class="p-4 text-right font-bold text-gray-800">SAR 1,210,000.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-[10px] uppercase font-bold">Posted</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary px-2" title="Bank File (SIF)"><i class="fas fa-file-invoice-dollar"></i></button>
                        <button class="text-gray-400 hover:text-blue-500 px-2" title="Journal Entry"><i class="fas fa-book"></i></button>
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