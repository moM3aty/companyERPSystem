<?php
// Path: resources/views/reports/dashboard.php
$pageTitle = 'Business Intelligence & Reports';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><i class="fas fa-chart-pie text-nour-primary mr-2"></i> Report Center</h1>
        <p class="text-sm text-gray-500 mt-1">Generate, schedule, and export business intelligence reports.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded text-sm font-medium hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fas fa-clock mr-1 text-gray-400"></i> Scheduled Jobs
        </button>
    </div>
</div>

<!-- Pinned Reports -->
<h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">Pinned & Favorites</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <a href="#" class="card p-4 hover:border-nour-primary transition-colors group cursor-pointer border-l-4 border-l-nour-primary flex flex-col justify-between min-h-[120px]">
        <div>
            <div class="flex justify-between items-start mb-2">
                <i class="fas fa-balance-scale text-nour-light text-xl"></i>
                <i class="fas fa-star text-yellow-400"></i>
            </div>
            <h4 class="font-bold text-gray-800 text-sm group-hover:text-nour-primary transition-colors">Trial Balance</h4>
        </div>
        <p class="text-xs text-gray-500">Complete GL trial balance with period comparisons.</p>
    </a>
    
    <a href="#" class="card p-4 hover:border-nour-primary transition-colors group cursor-pointer border-l-4 border-l-green-500 flex flex-col justify-between min-h-[120px]">
        <div>
            <div class="flex justify-between items-start mb-2">
                <i class="fas fa-chart-line text-green-500 text-xl"></i>
                <i class="fas fa-star text-yellow-400"></i>
            </div>
            <h4 class="font-bold text-gray-800 text-sm group-hover:text-nour-primary transition-colors">P&L Statement</h4>
        </div>
        <p class="text-xs text-gray-500">Income and expenses by cost center.</p>
    </a>

    <a href="#" class="card p-4 hover:border-nour-primary transition-colors group cursor-pointer border-l-4 border-l-blue-500 flex flex-col justify-between min-h-[120px]">
        <div>
            <div class="flex justify-between items-start mb-2">
                <i class="fas fa-boxes text-blue-500 text-xl"></i>
                <i class="fas fa-star text-yellow-400"></i>
            </div>
            <h4 class="font-bold text-gray-800 text-sm group-hover:text-nour-primary transition-colors">Inventory Valuation</h4>
        </div>
        <p class="text-xs text-gray-500">Moving average cost and stock quantities.</p>
    </a>

    <a href="#" class="card p-4 hover:border-nour-primary transition-colors group cursor-pointer border-l-4 border-l-purple-500 flex flex-col justify-between min-h-[120px]">
        <div>
            <div class="flex justify-between items-start mb-2">
                <i class="fas fa-money-check-alt text-purple-500 text-xl"></i>
                <i class="fas fa-star text-yellow-400"></i>
            </div>
            <h4 class="font-bold text-gray-800 text-sm group-hover:text-nour-primary transition-colors">Payroll Summary</h4>
        </div>
        <p class="text-xs text-gray-500">Net payouts and tax deductions by department.</p>
    </a>
</div>

<!-- Report Categories -->
<h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">Report Directories</h3>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="card p-0 overflow-hidden">
        <div class="p-3 bg-gray-50 border-b border-gray-100 font-bold text-gray-700 flex items-center"><i class="fas fa-file-invoice-dollar text-gray-400 mr-2 w-5 text-center"></i> Financials</div>
        <ul class="divide-y divide-gray-100 text-sm">
            <li><a href="#" class="block p-3 text-gray-600 hover:bg-nour-light/5 hover:text-nour-primary transition-colors">General Ledger</a></li>
            <li><a href="#" class="block p-3 text-gray-600 hover:bg-nour-light/5 hover:text-nour-primary transition-colors">Balance Sheet</a></li>
            <li><a href="#" class="block p-3 text-gray-600 hover:bg-nour-light/5 hover:text-nour-primary transition-colors">Cash Flow Statement</a></li>
            <li><a href="#" class="block p-3 text-gray-600 hover:bg-nour-light/5 hover:text-nour-primary transition-colors">Tax (VAT) Return</a></li>
        </ul>
    </div>
    
    <div class="card p-0 overflow-hidden">
        <div class="p-3 bg-gray-50 border-b border-gray-100 font-bold text-gray-700 flex items-center"><i class="fas fa-shopping-cart text-gray-400 mr-2 w-5 text-center"></i> Sales & CRM</div>
        <ul class="divide-y divide-gray-100 text-sm">
            <li><a href="#" class="block p-3 text-gray-600 hover:bg-nour-light/5 hover:text-nour-primary transition-colors">Sales by Product/Category</a></li>
            <li><a href="#" class="block p-3 text-gray-600 hover:bg-nour-light/5 hover:text-nour-primary transition-colors">Customer Aging (AR)</a></li>
            <li><a href="#" class="block p-3 text-gray-600 hover:bg-nour-light/5 hover:text-nour-primary transition-colors">Sales Commission Report</a></li>
            <li><a href="#" class="block p-3 text-gray-600 hover:bg-nour-light/5 hover:text-nour-primary transition-colors">Lead Conversion Rates</a></li>
        </ul>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="p-3 bg-gray-50 border-b border-gray-100 font-bold text-gray-700 flex items-center"><i class="fas fa-truck-loading text-gray-400 mr-2 w-5 text-center"></i> Supply Chain</div>
        <ul class="divide-y divide-gray-100 text-sm">
            <li><a href="#" class="block p-3 text-gray-600 hover:bg-nour-light/5 hover:text-nour-primary transition-colors">Stock Aging / Dead Stock</a></li>
            <li><a href="#" class="block p-3 text-gray-600 hover:bg-nour-light/5 hover:text-nour-primary transition-colors">Supplier Aging (AP)</a></li>
            <li><a href="#" class="block p-3 text-gray-600 hover:bg-nour-light/5 hover:text-nour-primary transition-colors">Landed Cost Analysis</a></li>
            <li><a href="#" class="block p-3 text-gray-600 hover:bg-nour-light/5 hover:text-nour-primary transition-colors">Purchasing Price Variance</a></li>
        </ul>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>