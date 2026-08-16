<?php
// Path: resources/views/search/results.php
$pageTitle = 'Search Results';
$query = htmlspecialchars($_GET['q'] ?? 'Alpha');
ob_start();
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Search Results for "<span class="text-nour-primary"><?= $query ?></span>"</h1>
    <p class="text-sm text-gray-500 mt-1">Found 6 results across all modules.</p>
</div>

<!-- Tabs -->
<div class="border-b border-gray-200 mb-6">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <a href="#" class="border-nour-primary text-nour-primary whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            All Results (6)
        </a>
        <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
            Customers (1)
        </a>
        <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
            Invoices & Orders (3)
        </a>
        <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
            Products (2)
        </a>
    </nav>
</div>

<div class="space-y-4 max-w-4xl">
    
    <!-- Result: Customer -->
    <div class="card p-4 hover:border-nour-primary transition-colors flex items-start gap-4">
        <div class="w-10 h-10 rounded bg-blue-100 text-blue-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-building"></i></div>
        <div class="flex-1">
            <div class="flex justify-between">
                <a href="/crm/customers/1" class="text-base font-bold text-nour-primary hover:underline">Alpha Tech Solutions</a>
                <span class="text-[10px] uppercase font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded">CRM</span>
            </div>
            <p class="text-sm text-gray-600 mt-1">Contact: Sarah Jenkins | Email: sarah@alphatech.com | Active Customer</p>
        </div>
    </div>

    <!-- Result: Invoice -->
    <div class="card p-4 hover:border-nour-primary transition-colors flex items-start gap-4">
        <div class="w-10 h-10 rounded bg-green-100 text-green-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-file-invoice-dollar"></i></div>
        <div class="flex-1">
            <div class="flex justify-between">
                <a href="/accounting/invoices/INV-2026-089" class="text-base font-bold text-nour-primary hover:underline">Sales Invoice #INV-2026-089</a>
                <span class="text-[10px] uppercase font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded">Accounting</span>
            </div>
            <p class="text-sm text-gray-600 mt-1">Billed to: <span class="font-bold">Alpha</span> Tech Solutions | Amount: $28,175.00 | Status: Paid</p>
        </div>
    </div>

    <!-- Result: Sales Order -->
    <div class="card p-4 hover:border-nour-primary transition-colors flex items-start gap-4">
        <div class="w-10 h-10 rounded bg-purple-100 text-purple-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-hand-holding-dollar"></i></div>
        <div class="flex-1">
            <div class="flex justify-between">
                <a href="/sales/orders/SO-2026-042" class="text-base font-bold text-nour-primary hover:underline">Sales Order #SO-2026-042</a>
                <span class="text-[10px] uppercase font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded">Sales</span>
            </div>
            <p class="text-sm text-gray-600 mt-1">Customer: <span class="font-bold">Alpha</span> Tech Solutions | Amount: $12,400.00 | Status: Processing</p>
        </div>
    </div>

    <!-- Result: Product -->
    <div class="card p-4 hover:border-nour-primary transition-colors flex items-start gap-4">
        <div class="w-10 h-10 rounded bg-orange-100 text-orange-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-box"></i></div>
        <div class="flex-1">
            <div class="flex justify-between">
                <a href="/inventory/products/99" class="text-base font-bold text-nour-primary hover:underline">Processor <span class="font-bold">Alpha</span>-Core X9</a>
                <span class="text-[10px] uppercase font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded">Inventory</span>
            </div>
            <p class="text-sm text-gray-600 mt-1">SKU: CHIP-ALPH-X9 | Category: Electronics | Price: $350.00</p>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>