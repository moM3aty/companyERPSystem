<?php
// Path: resources/views/shifts/show.php
$pageTitle = 'Shift Z-Report';
ob_start();
?>

<div class="max-w-4xl mx-auto mt-6">
    <div class="mb-6 flex justify-between items-end border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Shift Z-Report</h1>
            <p class="text-sm text-gray-500 font-mono mt-1">Ref: SHIFT-20260815-001</p>
        </div>
        <div class="flex gap-2">
            <button class="bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded text-sm font-medium hover:bg-gray-50 transition-colors shadow-sm" onclick="window.print()">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <a href="/shifts" class="bg-gray-100 border border-gray-300 text-gray-700 px-3 py-1.5 rounded text-sm font-medium hover:bg-gray-200 transition-colors shadow-sm">
                Back to List
            </a>
        </div>
    </div>

    <!-- Z-Report Container -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-8" id="z-report-print">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-12 h-12 mx-auto rounded-lg bg-nour-dark text-white font-bold text-xl flex items-center justify-center mb-2">NT</div>
            <h2 class="text-xl font-bold text-gray-900">Nour Trust Enterprise</h2>
            <p class="text-xs text-gray-500">HQ - Riyadh Branch</p>
            <p class="text-sm font-bold mt-2 uppercase tracking-widest text-nour-primary">End of Day (Z-Report)</p>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-8 text-sm">
            <div class="bg-gray-50 p-3 rounded border border-gray-100">
                <p class="text-gray-500 mb-1">Terminal</p>
                <p class="font-bold text-gray-800">TERM-01 (Main Register)</p>
            </div>
            <div class="bg-gray-50 p-3 rounded border border-gray-100">
                <p class="text-gray-500 mb-1">Cashier</p>
                <p class="font-bold text-gray-800">Ahmed Hassan</p>
            </div>
            <div class="bg-gray-50 p-3 rounded border border-gray-100">
                <p class="text-gray-500 mb-1">Opened At</p>
                <p class="font-medium text-gray-800">Aug 15, 2026 - 08:00 AM</p>
            </div>
            <div class="bg-gray-50 p-3 rounded border border-gray-100">
                <p class="text-gray-500 mb-1">Closed At</p>
                <p class="font-medium text-gray-800">Aug 15, 2026 - 04:30 PM</p>
            </div>
        </div>

        <!-- Sales Summary -->
        <div class="mb-8">
            <h3 class="text-sm font-bold text-gray-800 border-b border-gray-200 pb-2 mb-3 uppercase tracking-wider">Sales Summary</h3>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100">
                    <tr class="py-2 flex justify-between">
                        <td class="text-gray-600">Gross Sales</td>
                        <td class="font-medium">$2,100.00</td>
                    </tr>
                    <tr class="py-2 flex justify-between">
                        <td class="text-gray-600">Discounts Applied</td>
                        <td class="font-medium text-red-500">-$45.00</td>
                    </tr>
                    <tr class="py-2 flex justify-between">
                        <td class="text-gray-600">Net Sales</td>
                        <td class="font-medium">$2,055.00</td>
                    </tr>
                    <tr class="py-2 flex justify-between">
                        <td class="text-gray-600">Tax Collected (VAT 15%)</td>
                        <td class="font-medium">$308.25</td>
                    </tr>
                    <tr class="py-2 flex justify-between font-bold text-base border-t border-gray-300 mt-1 pt-2">
                        <td class="text-gray-900">Total Revenue</td>
                        <td class="text-nour-dark">$2,363.25</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Payment Methods -->
        <div class="mb-8">
            <h3 class="text-sm font-bold text-gray-800 border-b border-gray-200 pb-2 mb-3 uppercase tracking-wider">Tender Breakdown</h3>
            <table class="w-full text-sm text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="py-2 px-3 font-medium">Method</th>
                        <th class="py-2 px-3 font-medium text-center">Tx Count</th>
                        <th class="py-2 px-3 font-medium text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="py-2 px-3 text-gray-800">Cash</td>
                        <td class="py-2 px-3 text-center text-gray-600">14</td>
                        <td class="py-2 px-3 text-right font-medium">$1,250.00</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 text-gray-800">Credit Card</td>
                        <td class="py-2 px-3 text-center text-gray-600">8</td>
                        <td class="py-2 px-3 text-right font-medium">$1,113.25</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Cash Reconciliation -->
        <div class="mb-8 bg-gray-50 rounded-lg p-4 border border-gray-200">
            <h3 class="text-sm font-bold text-gray-800 mb-3 uppercase tracking-wider">Cash Reconciliation</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Opening Float</span>
                    <span class="font-medium">$500.00</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Cash Sales</span>
                    <span class="font-medium">+$1,250.00</span>
                </div>
                <div class="flex justify-between border-b border-gray-200 pb-2">
                    <span class="text-gray-600">Cash Refunds</span>
                    <span class="font-medium">-$45.00</span>
                </div>
                <div class="flex justify-between font-bold pt-1">
                    <span class="text-gray-800">Expected in Drawer</span>
                    <span class="text-gray-900">$1,705.00</span>
                </div>
                <div class="flex justify-between font-bold pt-1">
                    <span class="text-gray-800">Actual Counted</span>
                    <span class="text-gray-900">$1,705.00</span>
                </div>
                <div class="flex justify-between font-bold pt-2 mt-2 border-t border-gray-300">
                    <span class="text-gray-800">Discrepancy</span>
                    <span class="text-green-600">$0.00 (Balanced)</span>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-10 text-xs text-gray-400">
            <p>System Generated Report. GL Entries posted automatically.</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>