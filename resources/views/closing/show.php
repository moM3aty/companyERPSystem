<?php
// Path: resources/views/closing/show.php
$pageTitle = 'Period Closing Report';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 pb-4">
    <div>
        <a href="/closing" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Periods</a>
        <h1 class="text-2xl font-bold text-gray-800">Closing Report: <span class="text-nour-primary">August 2026</span></h1>
    </div>
    <div class="mt-4 sm:mt-0">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800 border border-green-200">
            <i class="fas fa-lock mr-2"></i> Period Locked
        </span>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card p-5 flex items-start gap-4">
        <div class="w-10 h-10 rounded-full bg-blue-50 text-nour-primary flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-boxes"></i></div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Final Inventory Value</p>
            <p class="text-xl font-bold text-gray-900">$1,452,000.50</p>
        </div>
    </div>
    <div class="card p-5 flex items-start gap-4">
        <div class="w-10 h-10 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-chart-line"></i></div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Depreciation Posted</p>
            <p class="text-xl font-bold text-gray-900">$12,450.00</p>
        </div>
    </div>
    <div class="card p-5 flex items-start gap-4">
        <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-file-invoice-dollar"></i></div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Net Income (Period)</p>
            <p class="text-xl font-bold text-gray-900">$85,200.00</p>
        </div>
    </div>
</div>

<div class="card p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Closing Checklist Audit</h3>
    <ul class="space-y-4">
        <li class="flex items-center text-sm">
            <i class="fas fa-check-circle text-green-500 text-lg mr-3"></i>
            <div>
                <p class="font-bold text-gray-800">Inventory Valuation Snapshot</p>
                <p class="text-xs text-gray-500">Executed on Aug 31, 23:55 by System.</p>
            </div>
        </li>
        <li class="flex items-center text-sm">
            <i class="fas fa-check-circle text-green-500 text-lg mr-3"></i>
            <div>
                <p class="font-bold text-gray-800">Fixed Assets Depreciation</p>
                <p class="text-xs text-gray-500">Depreciation run generated 45 journal entries.</p>
            </div>
        </li>
        <li class="flex items-center text-sm">
            <i class="fas fa-check-circle text-green-500 text-lg mr-3"></i>
            <div>
                <p class="font-bold text-gray-800">Bank Reconciliation</p>
                <p class="text-xs text-gray-500">All primary accounts reconciled successfully.</p>
            </div>
        </li>
        <li class="flex items-center text-sm">
            <i class="fas fa-check-circle text-green-500 text-lg mr-3"></i>
            <div>
                <p class="font-bold text-gray-800">Payroll Run</p>
                <p class="text-xs text-gray-500">PR-2026-08 posted. Net payout $45,200.</p>
            </div>
        </li>
    </ul>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>