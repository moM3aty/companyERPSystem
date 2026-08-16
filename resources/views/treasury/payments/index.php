<?php
// Path: resources/views/treasury/payments/index.php
$pageTitle = 'Payment Vouchers (Cash Out)';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><a href="/treasury/dashboard" class="hover:text-nour-primary transition-colors">Treasury</a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Payment Vouchers</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Payment Vouchers (Outgoing)</h1>
        <p class="text-sm text-gray-500 mt-1">Manage vendor payments, expense settlements, and payroll dispatches.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="btn-primary flex items-center shadow-md">
            <i class="fas fa-plus mr-2"></i> New Payment Voucher
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="card p-5 border-l-4 border-l-red-500 flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Total Paid (MTD)</p>
            <h3 class="text-xl font-bold text-gray-900">$215,400.00</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-red-50 text-red-500 flex items-center justify-center"><i class="fas fa-arrow-up"></i></div>
    </div>
    <div class="card p-5 border-l-4 border-l-yellow-500 flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Pending Approvals</p>
            <h3 class="text-xl font-bold text-gray-900">8</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center"><i class="fas fa-file-signature"></i></div>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Voucher No.</th>
                    <th class="p-4 font-semibold">Date</th>
                    <th class="p-4 font-semibold">Payee (Vendor/Employee)</th>
                    <th class="p-4 font-semibold">From Account</th>
                    <th class="p-4 font-semibold text-right">Amount</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 font-bold text-nour-primary">PV-2026-045</td>
                    <td class="p-4 text-gray-600">Aug 16, 2026</td>
                    <td class="p-4 font-medium text-gray-900">Global Tech Suppliers <span class="block text-[10px] text-gray-400">Bill: BILL-0801</span></td>
                    <td class="p-4 text-gray-600 text-xs">SABB Bank (Operations)</td>
                    <td class="p-4 text-right font-bold text-gray-900">$12,400.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-[10px] uppercase font-bold">Posted</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary px-2"><i class="fas fa-print"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 font-bold text-gray-600">PV-2026-046</td>
                    <td class="p-4 text-gray-600">Aug 16, 2026</td>
                    <td class="p-4 font-medium text-gray-900">Ahmed Hassan <span class="block text-[10px] text-gray-400">Travel Expense</span></td>
                    <td class="p-4 text-gray-600 text-xs">HQ Petty Cash</td>
                    <td class="p-4 text-right font-bold text-gray-900">$450.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded text-[10px] uppercase font-bold">Pending Appr.</span></td>
                    <td class="p-4 text-right">
                        <button class="text-green-500 hover:text-green-700 px-2" title="Approve"><i class="fas fa-check"></i></button>
                        <button class="text-gray-400 hover:text-blue-500 px-2"><i class="far fa-edit"></i></button>
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