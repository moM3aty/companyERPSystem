<?php
// Path: resources/views/reports/financial/index.php
$pageTitle = 'Financial Reports';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/reports" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Report Center</a>
        <h1 class="text-2xl font-bold text-gray-800">Financial Statements</h1>
        <p class="text-sm text-gray-500 mt-1">Generate General Ledger, Trial Balance, and P&L statements.</p>
    </div>
</div>

<div class="card p-6 mb-6 border-t-4 border-t-nour-primary">
    <form class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Select Report Type</label>
            <select class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm font-medium p-2 border bg-gray-50">
                <option>General Ledger (Account Statement)</option>
                <option selected>Trial Balance</option>
                <option>Income Statement (P&L)</option>
                <option>Balance Sheet</option>
                <option>ZATCA VAT Return Form</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Financial Period</label>
            <input type="month" value="<?= date('Y-m') ?>" class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm p-2 border bg-gray-50">
        </div>
        <div>
            <button type="button" class="w-full btn-primary py-2.5 shadow-md flex justify-center items-center">
                <i class="fas fa-cogs mr-2"></i> Generate
            </button>
        </div>
    </form>
</div>

<div class="card p-0 overflow-hidden">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Trial Balance Preview (August 2026)</h3>
        <button class="text-sm text-green-600 font-bold hover:underline"><i class="fas fa-file-excel mr-1"></i> Export XLSX</button>
    </div>
    <div class="p-6 text-center text-gray-500 py-16">
        <i class="fas fa-table text-4xl text-gray-300 mb-3"></i>
        <p>Select parameters and click generate to view the report data here.</p>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>