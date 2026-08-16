<?php
// Path: resources/views/enterprise/consolidation/eliminations.php
$pageTitle = 'Intercompany Eliminations';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Intercompany Eliminations</h1>
        <p class="text-sm text-gray-500 mt-1">Review and process automated elimination entries for group consolidated financials.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-calculator mr-2"></i> Recalculate
        </button>
        <button class="btn-primary shadow-md">
            <i class="fas fa-check-double mr-2"></i> Post Eliminations
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="card p-5 border-l-4 border-l-red-500">
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Total AP/AR Eliminated</h3>
        <p class="text-3xl font-black text-gray-900">$1,450,200.00</p>
        <p class="text-xs text-gray-400 mt-2">Balances cleared between sister companies.</p>
    </div>
    <div class="card p-5 border-l-4 border-l-blue-500">
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Revenue / COGS Eliminated</h3>
        <p class="text-3xl font-black text-gray-900">$850,000.00</p>
        <p class="text-xs text-gray-400 mt-2">Preventing double-counting of group revenue.</p>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Proposed Elimination Entries (Period: Aug 2026)</h3>
        <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded">Balanced</span>
    </div>
    <table class="w-full text-left text-sm whitespace-nowrap">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-200">
            <tr>
                <th class="p-4">Entity</th>
                <th class="p-4">GL Account</th>
                <th class="p-4">Description</th>
                <th class="p-4 text-right">Debit (DR)</th>
                <th class="p-4 text-right">Credit (CR)</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <tr class="hover:bg-gray-50">
                <td class="p-4 font-medium">Nour KSA</td>
                <td class="p-4 text-nour-primary font-mono">2105 - Intercompany AP</td>
                <td class="p-4 text-gray-500">Eliminate payable to Nour UAE</td>
                <td class="p-4 text-right font-bold text-gray-900">$150,000.00</td>
                <td class="p-4 text-right text-gray-400">-</td>
            </tr>
            <tr class="hover:bg-gray-50">
                <td class="p-4 font-medium">Nour UAE</td>
                <td class="p-4 text-nour-primary font-mono">1205 - Intercompany AR</td>
                <td class="p-4 text-gray-500">Eliminate receivable from Nour KSA</td>
                <td class="p-4 text-right text-gray-400">-</td>
                <td class="p-4 text-right font-bold text-gray-900">$150,000.00</td>
            </tr>
            <tr class="bg-gray-50/50 border-t-2 border-gray-200">
                <td colspan="3" class="p-4 text-right font-bold text-gray-800">Total Elimination</td>
                <td class="p-4 text-right font-black text-nour-dark">$150,000.00</td>
                <td class="p-4 text-right font-black text-nour-dark">$150,000.00</td>
            </tr>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>