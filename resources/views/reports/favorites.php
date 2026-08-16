<?php
// Path: resources/views/reports/favorites.php
$pageTitle = 'Favorite Reports';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/reports" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Report Center</a>
        <h1 class="text-2xl font-bold text-gray-800">My Favorite Reports</h1>
        <p class="text-sm text-gray-500 mt-1">Quick access to the reports you use most frequently.</p>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    
    <div class="card p-5 hover:border-nour-primary transition-colors group cursor-pointer border-t-4 border-t-nour-primary flex flex-col">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-lg bg-blue-50 text-nour-primary flex items-center justify-center text-2xl"><i class="fas fa-balance-scale"></i></div>
            <button class="text-yellow-400 hover:text-gray-300 transition-colors" title="Remove from favorites"><i class="fas fa-star text-lg"></i></button>
        </div>
        <h3 class="font-bold text-gray-900 text-lg mb-2 group-hover:text-nour-primary transition-colors">Trial Balance</h3>
        <p class="text-sm text-gray-500 mb-6 flex-1">Complete General Ledger trial balance showing debits and credits for a specific period.</p>
        <button class="w-full bg-gray-50 hover:bg-gray-100 text-nour-primary font-bold py-2 rounded border border-gray-200 transition-colors text-sm">
            Run Report
        </button>
    </div>
    
    <div class="card p-5 hover:border-green-500 transition-colors group cursor-pointer border-t-4 border-t-green-500 flex flex-col">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-lg bg-green-50 text-green-600 flex items-center justify-center text-2xl"><i class="fas fa-chart-line"></i></div>
            <button class="text-yellow-400 hover:text-gray-300 transition-colors" title="Remove from favorites"><i class="fas fa-star text-lg"></i></button>
        </div>
        <h3 class="font-bold text-gray-900 text-lg mb-2 group-hover:text-green-600 transition-colors">Income Statement (P&L)</h3>
        <p class="text-sm text-gray-500 mb-6 flex-1">Detailed breakdown of revenues, costs, and expenses to determine net profit.</p>
        <button class="w-full bg-gray-50 hover:bg-gray-100 text-green-600 font-bold py-2 rounded border border-gray-200 transition-colors text-sm">
            Run Report
        </button>
    </div>

    <div class="card p-5 hover:border-purple-500 transition-colors group cursor-pointer border-t-4 border-t-purple-500 flex flex-col">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-2xl"><i class="fas fa-money-check-alt"></i></div>
            <button class="text-yellow-400 hover:text-gray-300 transition-colors" title="Remove from favorites"><i class="fas fa-star text-lg"></i></button>
        </div>
        <h3 class="font-bold text-gray-900 text-lg mb-2 group-hover:text-purple-600 transition-colors">Payroll Summary</h3>
        <p class="text-sm text-gray-500 mb-6 flex-1">Monthly net payouts, tax deductions, and social insurance contributions by department.</p>
        <button class="w-full bg-gray-50 hover:bg-gray-100 text-purple-600 font-bold py-2 rounded border border-gray-200 transition-colors text-sm">
            Run Report
        </button>
    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>