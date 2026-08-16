<?php
// Path: resources/views/enterprise/intercompany/dashboard.php
$pageTitle = 'Intercompany Transactions';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Intercompany Reconciliation</h1>
        <p class="text-sm text-gray-500 mt-1">Match AP and AR between sister companies to resolve variances.</p>
    </div>
    <button class="btn-primary" onclick="showToast('Auto-matching algorithm started', 'info')">
        <i class="fas fa-magic mr-2"></i> Auto Match
    </button>
</div>

<div class="card p-0 overflow-hidden">
    <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <h3 class="font-bold text-gray-800">August 2026: Nour KSA ↔ Nour UAE</h3>
        <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded font-bold uppercase">Variance Detected</span>
    </div>
    
    <div class="grid grid-cols-3 divide-x divide-gray-100 text-center">
        <div class="p-6">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Receivables (KSA Books)</p>
            <p class="text-2xl font-bold text-gray-900">$150,000.00</p>
        </div>
        <div class="p-6">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Payables (UAE Books)</p>
            <p class="text-2xl font-bold text-gray-900">$148,500.00</p>
        </div>
        <div class="p-6 bg-red-50/30">
            <p class="text-xs font-bold text-red-500 uppercase tracking-wider mb-2">Unmatched Variance</p>
            <p class="text-2xl font-black text-red-600">$1,500.00</p>
        </div>
    </div>

    <div class="p-4 border-t border-gray-100">
        <p class="text-sm font-semibold text-gray-700 mb-3"><i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i> Detected Mismatches:</p>
        <ul class="text-sm text-gray-600 space-y-2 ml-6">
            <li>Invoice <strong>INV-KSA-99</strong> ($1,500) recorded in KSA AR on Aug 29, missing from UAE AP. <em>Reason: Goods in Transit.</em> <button class="text-nour-primary text-xs ml-2 hover:underline">Accept Variance</button></li>
        </ul>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>