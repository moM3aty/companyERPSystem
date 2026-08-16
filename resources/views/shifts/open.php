<?php
// Path: resources/views/shifts/open.php
$pageTitle = 'Open POS Shift';
ob_start();
?>

<div class="max-w-md mx-auto mt-10">
    <div class="text-center mb-6">
        <div class="w-16 h-16 mx-auto bg-nour-light/20 text-nour-primary rounded-full flex items-center justify-center text-2xl mb-3">
            <i class="fas fa-cash-register"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Start Cashier Session</h2>
        <p class="text-sm text-gray-500 mt-1">Enter your starting float amount to begin selling.</p>
    </div>

    <div class="card p-6 shadow-lg">
        <form action="/api/v1/pos/shifts" method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">POS Terminal</label>
                <select class="w-full border-gray-300 rounded-md focus:ring-nour-primary" name="terminal_id" required>
                    <option value="1">TERM-01 (Main Register)</option>
                    <option value="2">TERM-02 (Electronics Dept)</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Opening Cash (Float)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500">$</span>
                    </div>
                    <input type="number" step="0.01" name="opening_amount" value="500.00" required 
                        class="block w-full pl-8 pr-3 py-3 text-lg font-bold border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary">
                </div>
                <p class="text-xs text-gray-500 mt-1">Physical cash currently in the drawer.</p>
            </div>

            <div class="pt-4 border-t border-gray-100">
                <button type="submit" class="w-full btn-primary py-3 text-base flex justify-center items-center">
                    <i class="fas fa-lock-open mr-2"></i> Open Shift & Launch POS
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>