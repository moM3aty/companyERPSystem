<?php
// Path: resources/views/accounting/invoices.php
// Application-relative URL helper for /ERP/public hosting.
$appBaseUrl = rtrim(
    dirname($_SERVER['SCRIPT_NAME'] ?? '/ERP/public/index.php'),
    '/\\'
);

if ($appBaseUrl === '.' || $appBaseUrl === DIRECTORY_SEPARATOR) {
    $appBaseUrl = '';
}

$appUrl = static function (string $path = '') use ($appBaseUrl): string {
    $path = trim($path, '/');

    if ($path === '') {
        return $appBaseUrl !== '' ? $appBaseUrl . '/' : '/';
    }

    return $appBaseUrl . '/' . $path;
};

$appUrlEscaped = static function (string $path = '') use ($appUrl): string {
    return htmlspecialchars($appUrl($path), ENT_QUOTES, 'UTF-8');
};
$pageTitle = 'Invoices Management';
ob_start();
?>

<!-- Header & Breadcrumbs -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="<?= $appUrlEscaped('dashboard') ?>" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i> Home</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-xs mx-2"></i>
                        <span>Accounting</span>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-xs mx-2"></i>
                        <span class="text-gray-800 font-medium">Invoices</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Accounts Receivable (Invoices)</h1>
        <p class="text-sm text-gray-500 mt-1">Manage client billing, track payments, and follow up on overdue invoices.</p>
    </div>
    
    <div class="mt-4 sm:mt-0 flex gap-3">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors flex items-center">
            <i class="fas fa-chart-line mr-2 text-gray-400"></i> A/R Report
        </button>
        <button class="btn-primary flex items-center shadow-md shadow-nour-primary/30">
            <i class="fas fa-file-invoice mr-2"></i> Create Invoice
        </button>
    </div>
</div>

<!-- Financial Summary Widgets -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Outstanding -->
    <div class="card p-5 border-t-4 border-t-blue-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Total Outstanding</p>
                <h3 class="text-3xl font-bold text-gray-900">$124,500.00</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
        <div class="mt-4 text-sm text-gray-500">
            Across <span class="font-semibold text-gray-800">42</span> unpaid invoices.
        </div>
    </div>
    
    <!-- Overdue -->
    <div class="card p-5 border-t-4 border-t-red-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Overdue Amount</p>
                <h3 class="text-3xl font-bold text-red-600">$32,150.00</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-500">
                <i class="fas fa-exclamation-circle"></i>
            </div>
        </div>
        <div class="mt-4 text-sm text-gray-500">
            <span class="font-semibold text-red-600">8</span> invoices require immediate action.
        </div>
    </div>

    <!-- Paid this Month -->
    <div class="card p-5 border-t-4 border-t-green-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Paid (This Month)</p>
                <h3 class="text-3xl font-bold text-gray-900">$85,420.00</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-500">
                <i class="fas fa-check-double"></i>
            </div>
        </div>
        <div class="mt-4 text-sm text-gray-500">
            <span class="text-green-600 font-medium"><i class="fas fa-arrow-up mr-1"></i> 14%</span> vs last month.
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card p-4 mb-6 bg-white">
    <form action="<?= $appUrlEscaped('accounting/invoices') ?>" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
        <!-- Search -->
        <div class="flex-1 w-full">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Invoice / Customer</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" placeholder="INV-0000 or Company Name..." 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50">
            </div>
        </div>

        <!-- Date Range -->
        <div class="w-full md:w-56">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Date Range</label>
            <input type="date" name="date_from" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50 text-gray-500">
        </div>

        <!-- Status Filter -->
        <div class="w-full md:w-48">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</label>
            <select name="status" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50 cursor-pointer">
                <option value="">All Statuses</option>
                <option value="paid">Paid</option>
                <option value="unpaid">Unpaid</option>
                <option value="overdue">Overdue</option>
                <option value="partial">Partially Paid</option>
                <option value="draft">Draft</option>
            </select>
        </div>

        <!-- Filter Action -->
        <div class="w-full md:w-auto">
            <button type="submit" class="w-full md:w-auto bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md border border-gray-300 text-sm font-medium transition-colors">
                Apply Filters
            </button>
        </div>
    </form>
</div>

<!-- Invoices Table -->
<div class="card p-0 overflow-visible z-10 relative">
    <div class="table-responsive rounded-t-lg">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold w-8">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </th>
                    <th class="p-4 font-semibold">Invoice #</th>
                    <th class="p-4 font-semibold">Customer</th>
                    <th class="p-4 font-semibold">Issue Date</th>
                    <th class="p-4 font-semibold">Due Date</th>
                    <th class="p-4 font-semibold text-right">Amount</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                
                <!-- Overdue Invoice -->
                <tr class="hover:bg-red-50/20 transition-colors group bg-red-50/10">
                    <td class="p-4">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </td>
                    <td class="p-4">
                        <a href="<?= $appUrlEscaped('accounting/invoices/INV-2024-089') ?>" class="font-bold text-nour-primary hover:underline">INV-2024-089</a>
                        <p class="text-xs text-gray-400 mt-0.5">PO: #44021</p>
                    </td>
                    <td class="p-4">
                        <span class="font-semibold text-gray-900">Alpha Tech Solutions</span>
                        <p class="text-xs text-gray-500 mt-0.5">Contact: Sarah Jenkins</p>
                    </td>
                    <td class="p-4 text-gray-600">Jul 15, 2024</td>
                    <td class="p-4 font-medium text-red-600">
                        Aug 15, 2024
                        <span class="block text-[10px] text-red-400">10 Days Overdue</span>
                    </td>
                    <td class="p-4 text-right">
                        <span class="font-bold text-gray-900">$12,500.00</span>
                    </td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Overdue
                        </span>
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-2 py-1 rounded text-xs font-medium transition-colors">
                                Record Payment
                            </button>
                            <button class="text-gray-400 hover:text-nour-primary transition-colors p-1" title="Send Reminder">
                                <i class="far fa-paper-plane"></i>
                            </button>
                            <button class="text-gray-400 hover:text-gray-700 transition-colors p-1" title="Download PDF">
                                <i class="fas fa-file-pdf"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Unpaid Invoice -->
                <tr class="hover:bg-gray-50/80 transition-colors group">
                    <td class="p-4">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </td>
                    <td class="p-4">
                        <a href="<?= $appUrlEscaped('accounting/invoices/INV-2024-092') ?>" class="font-bold text-nour-primary hover:underline">INV-2024-092</a>
                    </td>
                    <td class="p-4">
                        <span class="font-semibold text-gray-900">Global Pharma Group</span>
                    </td>
                    <td class="p-4 text-gray-600">Aug 20, 2024</td>
                    <td class="p-4 text-gray-600">Sep 20, 2024</td>
                    <td class="p-4 text-right">
                        <span class="font-bold text-gray-900">$8,200.50</span>
                    </td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Unpaid
                        </span>
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button class="text-gray-400 hover:text-gray-700 transition-colors p-1" title="View">
                                <i class="far fa-eye"></i>
                            </button>
                            <button class="text-gray-400 hover:text-gray-700 transition-colors p-1" title="Download PDF">
                                <i class="fas fa-file-pdf"></i>
                            </button>
                            <button class="text-gray-400 hover:text-gray-700 transition-colors p-1 dropdown-menu-trigger">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Partially Paid Invoice -->
                <tr class="hover:bg-gray-50/80 transition-colors group">
                    <td class="p-4">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </td>
                    <td class="p-4">
                        <a href="<?= $appUrlEscaped('accounting/invoices/INV-2024-085') ?>" class="font-bold text-nour-primary hover:underline">INV-2024-085</a>
                    </td>
                    <td class="p-4">
                        <span class="font-semibold text-gray-900">Royal Manufacturing</span>
                    </td>
                    <td class="p-4 text-gray-600">Aug 01, 2024</td>
                    <td class="p-4 text-gray-600">Aug 30, 2024</td>
                    <td class="p-4 text-right">
                        <span class="font-bold text-gray-900">$45,000.00</span>
                        <p class="text-xs text-gray-500 mt-0.5">Bal: $15,000.00</p>
                    </td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Partial (66%)
                        </span>
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                             <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-2 py-1 rounded text-xs font-medium transition-colors">
                                Record Payment
                            </button>
                            <button class="text-gray-400 hover:text-gray-700 transition-colors p-1" title="Download PDF">
                                <i class="fas fa-file-pdf"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Paid Invoice -->
                <tr class="hover:bg-gray-50/80 transition-colors group opacity-80">
                    <td class="p-4">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </td>
                    <td class="p-4">
                        <a href="<?= $appUrlEscaped('accounting/invoices/INV-2024-081') ?>" class="font-bold text-gray-600 hover:text-nour-primary transition-colors">INV-2024-081</a>
                    </td>
                    <td class="p-4">
                        <span class="font-semibold text-gray-600">Style & Fashion Retail</span>
                    </td>
                    <td class="p-4 text-gray-500">Jul 10, 2024</td>
                    <td class="p-4 text-gray-500">Aug 10, 2024</td>
                    <td class="p-4 text-right">
                        <span class="font-medium text-gray-600">$3,400.00</span>
                    </td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Paid
                        </span>
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <span class="text-xs text-green-600 font-medium mr-2"><i class="fas fa-check"></i> Settled</span>
                            <button class="text-gray-400 hover:text-gray-700 transition-colors p-1" title="View Receipt">
                                <i class="fas fa-receipt"></i>
                            </button>
                        </div>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
    
    <!-- Pagination Footer -->
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 rounded-b-lg">
        <div class="flex items-center justify-between">
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium text-gray-900">1</span> to <span class="font-medium text-gray-900">4</span> of <span class="font-medium text-gray-900">156</span> invoices
                    </p>
                </div>
                <div>
                    <!-- Simple Pagination mockup to match other pages -->
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Previous</span>
                            <i class="fas fa-chevron-left text-xs"></i>
                        </a>
                        <a href="#" aria-current="page" class="z-10 bg-nour-primary/10 border-nour-primary text-nour-primary relative inline-flex items-center px-4 py-2 border text-sm font-medium"> 1 </a>
                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium"> 2 </a>
                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 hidden md:inline-flex relative items-center px-4 py-2 border text-sm font-medium"> 3 </a>
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Next</span>
                            <i class="fas fa-chevron-right text-xs"></i>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>