<?php
// Path: resources/views/manufacturing/bom/create.php
$pageTitle = 'Create Bill of Materials';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/manufacturing/bom" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to BOMs</a>
        <h1 class="text-2xl font-bold text-gray-800">New Bill of Materials</h1>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-3">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded shadow-sm text-sm font-medium hover:bg-gray-50">Cancel</button>
        <button class="btn-primary shadow-md">Save BOM</button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Header Details -->
    <div class="col-span-1 lg:col-span-3 card p-5 border-t-4 border-t-nour-primary">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Finished Product <span class="text-red-500">*</span></label>
                <select class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm font-medium">
                    <option value="">Select a storable product...</option>
                    <option value="1">Custom Server Rack Unit - 42U</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                <div class="flex gap-2 items-center">
                    <input type="number" value="1" class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm text-right">
                    <span class="text-gray-500 font-medium text-sm">Units</span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">BOM Type</label>
                <div class="flex flex-col gap-2 mt-2">
                    <label class="inline-flex items-center text-sm text-gray-700">
                        <input type="radio" name="bom_type" class="text-nour-primary focus:ring-nour-primary mr-2" checked> Manufacture
                    </label>
                    <label class="inline-flex items-center text-sm text-gray-700">
                        <input type="radio" name="bom_type" class="text-nour-primary focus:ring-nour-primary mr-2"> Kit (Phantom)
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Components & Routing Tabs -->
    <div class="col-span-1 lg:col-span-3">
        <div class="border-b border-gray-200 mb-4">
            <nav class="-mb-px flex space-x-8">
                <a href="#" class="border-nour-primary text-nour-primary whitespace-nowrap py-3 px-1 border-b-2 font-bold text-sm flex items-center"><i class="fas fa-boxes mr-2"></i> Components (Raw Materials)</a>
                <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center transition-colors"><i class="fas fa-route mr-2"></i> Routing (Operations)</a>
            </nav>
        </div>

        <div class="card p-0">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 border-b border-gray-200">
                        <th class="py-3 px-4 font-semibold w-1/2">Component Product</th>
                        <th class="py-3 px-4 font-semibold w-32 text-center">Quantity</th>
                        <th class="py-3 px-4 font-semibold w-24">UoM</th>
                        <th class="py-3 px-4 font-semibold w-48">Consumed in Operation</th>
                        <th class="py-3 px-4 font-semibold w-10 text-center"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="py-2 px-4">
                            <select class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5">
                                <option>Steel Frame (Raw) - SKU: RAW-STL-01</option>
                            </select>
                        </td>
                        <td class="py-2 px-4"><input type="number" value="1.00" class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5 text-center"></td>
                        <td class="py-2 px-4 text-gray-600 font-medium mt-1.5">Unit</td>
                        <td class="py-2 px-4">
                            <select class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5">
                                <option>Op1 - Frame Assembly</option>
                            </select>
                        </td>
                        <td class="py-2 px-4 text-center"><button class="text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button></td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4">
                            <select class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5">
                                <option>Copper Wire Spool - SKU: RAW-CBL-100</option>
                            </select>
                        </td>
                        <td class="py-2 px-4"><input type="number" value="5.50" class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5 text-center"></td>
                        <td class="py-2 px-4 text-gray-600 font-medium mt-1.5">Meter</td>
                        <td class="py-2 px-4">
                            <select class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5">
                                <option>Op2 - Wiring & Electrical</option>
                            </select>
                        </td>
                        <td class="py-2 px-4 text-center"><button class="text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button></td>
                    </tr>
                </tbody>
            </table>
            <div class="p-4 border-t border-gray-100">
                <button class="text-sm font-bold text-nour-primary hover:text-nour-dark"><i class="fas fa-plus-circle mr-1"></i> Add Component</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>