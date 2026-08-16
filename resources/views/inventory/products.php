<?php
// Path: resources/views/inventory/products.php
$pageTitle = 'Products & Inventory';
ob_start();
?>

<!-- Header & Breadcrumbs -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i> Home</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-xs mx-2"></i>
                        <span>Inventory</span>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-xs mx-2"></i>
                        <span class="text-gray-800 font-medium">Products</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Products Catalog</h1>
        <p class="text-sm text-gray-500 mt-1">Manage inventory items, pricing, and track stock levels across warehouses.</p>
    </div>
    
    <div class="mt-4 sm:mt-0 flex gap-3">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors flex items-center">
            <i class="fas fa-cloud-upload-alt mr-2 text-gray-400"></i> Import
        </button>
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors flex items-center">
            <i class="fas fa-file-export mr-2 text-gray-400"></i> Export
        </button>
        <button class="btn-primary flex items-center shadow-md shadow-nour-primary/30">
            <i class="fas fa-plus mr-2"></i> Add Product
        </button>
    </div>
</div>

<!-- Quick Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-blue-50 text-nour-primary flex items-center justify-center text-xl flex-shrink-0">
            <i class="fas fa-boxes"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Total Items</p>
            <h4 class="text-xl font-bold text-gray-800">2,451</h4>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-xl flex-shrink-0">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Low Stock Alerts</p>
            <h4 class="text-xl font-bold text-gray-800">18</h4>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-green-50 text-green-600 flex items-center justify-center text-xl flex-shrink-0">
            <i class="fas fa-tags"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Active Categories</p>
            <h4 class="text-xl font-bold text-gray-800">34</h4>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xl flex-shrink-0">
            <i class="fas fa-chart-pie"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Total Valuation</p>
            <h4 class="text-xl font-bold text-gray-800">$452.8K</h4>
        </div>
    </div>
</div>

<!-- Filters and Search Section -->
<div class="card p-4 mb-6 bg-white">
    <form action="/inventory/products" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        <!-- Search -->
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Search Product</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" placeholder="SKU, Name, or Barcode..." 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50">
            </div>
        </div>

        <!-- Category Filter -->
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Category</label>
            <select name="category" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50 cursor-pointer">
                <option value="">All Categories</option>
                <option value="electronics">Electronics</option>
                <option value="hardware">Hardware</option>
                <option value="software">Software Licenses</option>
                <option value="office_supplies">Office Supplies</option>
            </select>
        </div>

        <!-- Stock Status Filter -->
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Stock Status</label>
            <select name="stock_status" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50 cursor-pointer">
                <option value="">Any Status</option>
                <option value="in_stock">In Stock</option>
                <option value="low_stock">Low Stock</option>
                <option value="out_of_stock">Out of Stock</option>
            </select>
        </div>

        <!-- Filter Action -->
        <div>
            <button type="submit" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md border border-gray-300 text-sm font-medium transition-colors">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
        </div>
    </form>
</div>

<!-- Data Table Section -->
<div class="card p-0 overflow-visible z-10 relative">
    <div class="table-responsive rounded-t-lg">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold w-8">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </th>
                    <th class="p-4 font-semibold">Product Info</th>
                    <th class="p-4 font-semibold">Category</th>
                    <th class="p-4 font-semibold">Pricing</th>
                    <th class="p-4 font-semibold w-48">Stock Level</th>
                    <th class="p-4 font-semibold">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                
                <!-- Product Row 1 (In Stock) -->
                <tr class="hover:bg-gray-50/80 transition-colors group">
                    <td class="p-4">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded border border-gray-200 bg-white flex items-center justify-center p-1 flex-shrink-0">
                                <img src="https://placehold.co/100x100/f3f4f6/a1a1aa?text=Laptop" alt="Product" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <a href="/inventory/products/1" class="font-semibold text-gray-900 group-hover:text-nour-primary transition-colors block">ThinkPad X1 Carbon Gen 10</a>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-500 font-mono">SKU: LNV-X1-G10</span>
                                    <span class="text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">Main WH</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="text-gray-800">Electronics</span>
                        <p class="text-xs text-gray-500 mt-0.5">Laptops & Computers</p>
                    </td>
                    <td class="p-4">
                        <div class="font-semibold text-gray-900">$1,450.00</div>
                        <div class="text-xs text-gray-500 mt-0.5">Cost: $1,100.00</div>
                    </td>
                    <td class="p-4">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-gray-700">145 Units</span>
                            <span class="text-gray-400">Max: 200</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-green-500 h-1.5 rounded-full" style="width: 72.5%"></div>
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                            Active
                        </span>
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button class="text-gray-400 hover:text-nour-primary transition-colors p-1" title="View History">
                                <i class="fas fa-history"></i>
                            </button>
                            <a href="/inventory/products/1/edit" class="text-gray-400 hover:text-blue-500 transition-colors p-1" title="Edit">
                                <i class="far fa-edit"></i>
                            </a>
                            <button class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Delete">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Product Row 2 (Low Stock) -->
                <tr class="hover:bg-red-50/30 transition-colors group bg-red-50/10">
                    <td class="p-4">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded border border-gray-200 bg-white flex items-center justify-center p-1 flex-shrink-0">
                                <img src="https://placehold.co/100x100/f3f4f6/a1a1aa?text=Monitor" alt="Product" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <a href="/inventory/products/2" class="font-semibold text-gray-900 group-hover:text-nour-primary transition-colors block">Dell UltraSharp 27" 4K</a>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-500 font-mono">SKU: DLL-U2723QE</span>
                                    <span class="text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">Riyadh WH</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="text-gray-800">Electronics</span>
                        <p class="text-xs text-gray-500 mt-0.5">Monitors</p>
                    </td>
                    <td class="p-4">
                        <div class="font-semibold text-gray-900">$580.00</div>
                        <div class="text-xs text-gray-500 mt-0.5">Cost: $420.00</div>
                    </td>
                    <td class="p-4">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-bold text-red-600">8 Units (Low)</span>
                            <span class="text-gray-400">Min: 15</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-red-500 h-1.5 rounded-full animate-pulse" style="width: 15%"></div>
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                            Active
                        </span>
                    </td>
                    <td class="p-4 text-right">
                        <button class="bg-nour-primary/10 text-nour-primary hover:bg-nour-primary hover:text-white px-2 py-1 rounded text-xs font-medium transition-colors mr-2">
                            Reorder
                        </button>
                        <a href="#" class="text-gray-400 hover:text-blue-500 transition-colors p-1" title="Edit">
                            <i class="far fa-edit"></i>
                        </a>
                    </td>
                </tr>

                <!-- Product Row 3 (Out of stock & Inactive) -->
                <tr class="hover:bg-gray-50/80 transition-colors group opacity-75">
                    <td class="p-4">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary" disabled>
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded border border-gray-200 bg-gray-100 flex items-center justify-center p-1 flex-shrink-0 grayscale">
                                <i class="fas fa-box-open text-gray-400 text-xl"></i>
                            </div>
                            <div>
                                <a href="/inventory/products/3" class="font-semibold text-gray-500 group-hover:text-gray-700 transition-colors block line-through">Wireless Mouse M330</a>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-400 font-mono">SKU: LOG-M330</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 text-gray-500">Accessories</td>
                    <td class="p-4 text-gray-500">
                        <div>$25.00</div>
                        <div class="text-xs">Cost: $12.50</div>
                    </td>
                    <td class="p-4">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-bold text-gray-500">0 Units</span>
                            <span class="text-gray-400">Min: 50</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-gray-400 h-1.5 rounded-full" style="width: 0%"></div>
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                            Inactive / Discontinued
                        </span>
                    </td>
                    <td class="p-4 text-right">
                        <a href="#" class="text-gray-400 hover:text-blue-500 transition-colors p-1" title="View">
                            <i class="far fa-eye"></i>
                        </a>
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
                        Showing <span class="font-medium text-gray-900">1</span> to <span class="font-medium text-gray-900">3</span> of <span class="font-medium text-gray-900">2,451</span> products
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Previous</span>
                            <i class="fas fa-chevron-left text-xs"></i>
                        </a>
                        <a href="#" aria-current="page" class="z-10 bg-nour-primary/10 border-nour-primary text-nour-primary relative inline-flex items-center px-4 py-2 border text-sm font-medium"> 1 </a>
                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium"> 2 </a>
                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 hidden md:inline-flex relative items-center px-4 py-2 border text-sm font-medium"> 3 </a>
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"> ... </span>
                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium"> 246 </a>
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