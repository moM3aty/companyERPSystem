<?php
// Path: resources/views/enterprise/consolidation/reports.php
$pageTitle = 'Consolidated Financial Reports';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Consolidated Reports</h1>
        <p class="text-sm text-gray-500 mt-1">Generate multi-entity financial statements with real-time eliminations.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-file-excel mr-2 text-green-600"></i> Export to Excel
        </button>
        <button class="btn-primary shadow-md">
            <i class="fas fa-print mr-2"></i> Print Report
        </button>
    </div>
</div>

<div class="card p-5 mb-6 bg-white">
    <form class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Consolidation Group</label>
            <select class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm font-medium">
                <option>Nour Trust Holdings (All Entities)</option>
                <option>Nour GCC (KSA & UAE Only)</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Report Type</label>
            <select class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm font-medium">
                <option>Consolidated Income Statement (P&L)</option>
                <option>Consolidated Balance Sheet</option>
                <option>Consolidated Cash Flow</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Financial Period</label>
            <input type="month" value="2026-08" class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm font-medium">
        </div>
        <div>
            <button type="button" class="w-full bg-nour-dark text-white hover:bg-black px-4 py-2 rounded-md text-sm font-bold transition-colors shadow-sm">
                Generate
            </button>
        </div>
    </form>
</div>

<div class="card p-0 overflow-hidden">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <div>
            <h3 class="font-bold text-gray-900 text-lg">Consolidated Income Statement</h3>
            <p class="text-xs text-gray-500">Period: August 2026 | Base Currency: USD ($)</p>
        </div>
        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-bold rounded border border-green-200"><i class="fas fa-check-circle mr-1"></i> Eliminations Applied</span>
    </div>
    <div class="table-responsive">
        <table class="w-full text-left text-sm border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider border-b-2 border-gray-200">
                    <th class="p-3 font-bold">GL Account</th>
                    <th class="p-3 text-right font-semibold text-blue-800">Nour KSA</th>
                    <th class="p-3 text-right font-semibold text-blue-800">Nour UAE</th>
                    <th class="p-3 text-right font-semibold text-red-600">Elimination Dr/Cr</th>
                    <th class="p-3 text-right font-black text-gray-900 bg-gray-200">Consolidated Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <!-- Revenue Group -->
                <tr class="bg-gray-50 font-bold text-gray-800">
                    <td class="p-3" colspan="5">1. Operating Revenue</td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-3 pl-8 text-gray-700">4000 - Sales of Goods</td>
                    <td class="p-3 text-right">1,250,000.00</td>
                    <td class="p-3 text-right">840,000.00</td>
                    <td class="p-3 text-right text-red-500">-120,000.00</td>
                    <td class="p-3 text-right font-bold bg-gray-50">1,970,000.00</td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-3 pl-8 text-gray-700">4100 - Intercompany Sales</td>
                    <td class="p-3 text-right">120,000.00</td>
                    <td class="p-3 text-right">50,000.00</td>
                    <td class="p-3 text-right text-red-500">-170,000.00</td>
                    <td class="p-3 text-right font-bold bg-gray-50">0.00</td>
                </tr>
                <!-- COGS Group -->
                <tr class="bg-gray-50 font-bold text-gray-800 mt-2 border-t-2 border-gray-200">
                    <td class="p-3" colspan="5">2. Cost of Goods Sold (COGS)</td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-3 pl-8 text-gray-700">5000 - Cost of Sales</td>
                    <td class="p-3 text-right">-650,000.00</td>
                    <td class="p-3 text-right">-420,000.00</td>
                    <td class="p-3 text-right text-green-600">+120,000.00</td>
                    <td class="p-3 text-right font-bold bg-gray-50">-950,000.00</td>
                </tr>
                <!-- Gross Profit -->
                <tr class="bg-nour-dark text-white font-black text-base border-t-4 border-gray-300">
                    <td class="p-4 uppercase tracking-wider">Gross Profit</td>
                    <td class="p-4 text-right">720,000.00</td>
                    <td class="p-4 text-right">470,000.00</td>
                    <td class="p-4 text-right">-170,000.00</td>
                    <td class="p-4 text-right text-nour-light bg-black/20">1,020,000.00</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>