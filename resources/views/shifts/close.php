<?php
// Path: resources/views/shifts/close.php
$pageTitle = 'Close POS Shift';
ob_start();
?>

<div class="max-w-3xl mx-auto mt-8">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a></li>
                    <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><a href="/shifts" class="hover:text-nour-primary">Shifts</a></li>
                    <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Close Shift</span></li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-gray-800">Close Cashier Shift</h1>
            <p class="text-sm text-gray-500 mt-1">Terminal: TERM-01 (Main Register) | Cashier: Ahmed Hassan</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span> Shift Active (Opened 08:00 AM)
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Left: Expected System Totals -->
        <div class="card p-0 overflow-hidden">
            <div class="p-4 bg-gray-50 border-b border-gray-100">
                <h3 class="font-bold text-gray-800"><i class="fas fa-calculator mr-2 text-nour-primary"></i> System Expected Totals</h3>
            </div>
            <div class="p-5 space-y-4">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Opening Cash (Float)</span>
                    <span class="font-medium text-gray-900">$500.00</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Cash Sales (14 Orders)</span>
                    <span class="font-medium text-green-600">+$1,250.00</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Cash Refunds (1 Return)</span>
                    <span class="font-medium text-red-600">-$45.00</span>
                </div>
                <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                    <span class="font-bold text-gray-800">Total Expected Cash</span>
                    <span class="text-xl font-black text-nour-dark">$1,705.00</span>
                </div>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-100">
                <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Other Tender Types</h4>
                <div class="flex justify-between items-center text-sm mb-1">
                    <span class="text-gray-600"><i class="far fa-credit-card mr-1"></i> Credit/Debit Card</span>
                    <span class="font-medium text-gray-900">$840.50</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600"><i class="fas fa-money-check mr-1"></i> Bank Transfer</span>
                    <span class="font-medium text-gray-900">$0.00</span>
                </div>
            </div>
        </div>

        <!-- Right: Physical Count & Close Action -->
        <div class="card p-0 overflow-hidden flex flex-col">
            <div class="p-4 bg-gray-50 border-b border-gray-100">
                <h3 class="font-bold text-gray-800"><i class="fas fa-hand-holding-usd mr-2 text-nour-primary"></i> Physical Cash Count</h3>
            </div>
            <form action="/api/v1/pos/shifts/1/close" method="POST" class="p-5 flex-1 flex flex-col">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Enter Actual Cash in Drawer</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-bold">$</span>
                        </div>
                        <input type="number" step="0.01" name="actual_cash_counted" required placeholder="0.00"
                            class="block w-full pl-8 pr-4 py-4 text-2xl font-black border border-gray-300 rounded-lg focus:ring-nour-primary focus:border-nour-primary bg-yellow-50 text-gray-900">
                    </div>
                    <p class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle text-nour-light"></i> Any discrepancy will be automatically logged to the discrepancy GL account.</p>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Closing Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm" placeholder="Explain discrepancies if any..."></textarea>
                </div>

                <div class="mt-auto pt-4 border-t border-gray-100">
                    <button type="submit" class="w-full btn-primary py-3 text-base flex justify-center items-center shadow-lg bg-red-600 hover:bg-red-700 border-none transition-colors" onclick="return confirm('Are you sure you want to close this shift? This action generates the Z-Report and cannot be undone.')">
                        <i class="fas fa-lock mr-2"></i> Close Shift & Print Z-Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>