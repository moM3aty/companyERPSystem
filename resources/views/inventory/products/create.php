<?php
// Path: resources/views/inventory/products/create.php
$pageTitle = 'Add New Product';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/inventory/products" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Catalog</a>
        <h1 class="text-2xl font-bold text-gray-800">Add New Product</h1>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-3">
        <button type="submit" form="productForm" class="btn-primary px-8 shadow-md"><i class="fas fa-save mr-2"></i> Save Product</button>
    </div>
</div>

<form id="productForm" action="/api/v1/inventory/products" method="POST">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">General Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="e.g., Dell XPS 15 Laptop" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Product Code (SKU) <span class="text-red-500">*</span></label>
                        <input type="text" name="code" required placeholder="DLL-XPS-15" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm font-mono uppercase">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Barcode (EAN/UPC)</label>
                        <div class="flex gap-2">
                            <input type="text" name="barcode" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm font-mono">
                            <button type="button" class="bg-gray-100 border border-gray-300 px-3 rounded-md hover:bg-gray-200" title="Generate"><i class="fas fa-barcode"></i></button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                        <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="">Select Category</option>
                            <option value="1">Electronics > Computers</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Product Type <span class="text-red-500">*</span></label>
                        <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm bg-blue-50/50">
                            <option value="storable">Storable Product (Track Stock)</option>
                            <option value="consumable">Consumable</option>
                            <option value="service">Service (No Stock)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Internal Description</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm"></textarea>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Pricing & Costs</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Sales Price</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500">$</span></div>
                            <input type="number" step="0.01" name="default_price" value="0.00" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary text-right font-bold text-gray-900">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Standard Cost</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500">$</span></div>
                            <input type="number" step="0.01" name="standard_cost" value="0.00" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary text-right text-gray-600">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Costing Method</label>
                        <select name="cost_method" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="average">Moving Average Cost (AVCO)</option>
                            <option value="fifo">First In, First Out (FIFO)</option>
                            <option value="standard">Standard Cost</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Default Tax</label>
                        <select name="default_tax_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="1">VAT 15%</option>
                            <option value="2">Zero Rated 0%</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Settings -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Image -->
            <div class="card p-6 text-center">
                <div class="w-32 h-32 mx-auto border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 mb-4 cursor-pointer hover:bg-gray-100 transition-colors">
                    <div class="text-center">
                        <i class="fas fa-camera text-2xl mb-1"></i>
                        <p class="text-xs font-medium">Upload Image</p>
                    </div>
                </div>
                <input type="file" class="hidden" accept="image/*">
            </div>

            <!-- Traceability -->
            <div class="card p-6 bg-gray-50 border-t-4 border-t-nour-primary">
                <h3 class="text-sm font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2 uppercase tracking-wider">Traceability</h3>
                <div class="space-y-3">
                    <label class="flex items-center text-sm text-gray-700">
                        <input type="checkbox" name="track_batches" class="rounded text-nour-primary focus:ring-nour-primary mr-2 w-4 h-4">
                        Track by Lots / Batches
                    </label>
                    <label class="flex items-center text-sm text-gray-700">
                        <input type="checkbox" name="track_serials" class="rounded text-nour-primary focus:ring-nour-primary mr-2 w-4 h-4">
                        Track by Unique Serial Number
                    </label>
                </div>
            </div>

            <!-- Options -->
            <div class="card p-6">
                <h3 class="text-sm font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2 uppercase tracking-wider">Options</h3>
                <div class="space-y-4">
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="text-sm font-medium text-gray-700">Can be Sold</span>
                        <input type="checkbox" checked class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500 relative"></div>
                    </label>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="text-sm font-medium text-gray-700">Can be Purchased</span>
                        <input type="checkbox" checked class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500 relative"></div>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>