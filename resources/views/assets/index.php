<?php
// Path: resources/views/assets/index.php
$pageTitle = 'Fixed Assets Register';
ob_start();
?>

<!-- Header & Breadcrumbs -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a>
                </li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Fixed Assets</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Fixed Assets Register</h1>
        <p class="text-sm text-gray-500 mt-1">Manage company property, calculate depreciation, and track asset locations.</p>
    </div>
    
    <div class="mt-4 sm:mt-0 flex gap-3">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors flex items-center">
            <i class="fas fa-calculator mr-2 text-gray-400"></i> Run Depreciation
        </button>
        <button class="btn-primary flex items-center shadow-md shadow-nour-primary/30">
            <i class="fas fa-plus mr-2"></i> Register Asset
        </button>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-blue-50 text-nour-primary flex items-center justify-center text-xl flex-shrink-0"><i class="fas fa-building"></i></div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Total Active Assets</p>
            <h4 class="text-xl font-bold text-gray-800">142</h4>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-green-50 text-green-600 flex items-center justify-center text-xl flex-shrink-0"><i class="fas fa-money-check-alt"></i></div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Gross Purchase Value</p>
            <h4 class="text-xl font-bold text-gray-800">$2.4M</h4>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl flex-shrink-0"><i class="fas fa-chart-area"></i></div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Accum. Depreciation</p>
            <h4 class="text-xl font-bold text-gray-800">$840K</h4>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xl flex-shrink-0"><i class="fas fa-tag"></i></div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Net Book Value</p>
            <h4 class="text-xl font-bold text-gray-800">$1.56M</h4>
        </div>
    </div>
</div>

<!-- Assets Table -->
<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-4 border-b border-gray-100 flex flex-wrap gap-4 items-center justify-between bg-gray-50/50">
        <div class="flex gap-2 w-full md:w-auto">
            <input type="text" placeholder="Search Asset Code or Name..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-full md:w-64">
            <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600 hidden md:block">
                <option>All Categories</option>
                <option>Vehicles</option>
                <option>IT Equipment</option>
                <option>Furniture & Fixtures</option>
                <option>Machinery</option>
            </select>
        </div>
        <button class="bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-md text-sm font-medium hover:bg-gray-100 hidden md:block">
            <i class="fas fa-filter mr-1"></i> More Filters
        </button>
    </div>
    <div class="table-responsive rounded-b-lg">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Asset Code & Name</th>
                    <th class="p-4 font-semibold">Category</th>
                    <th class="p-4 font-semibold text-right">Purchase Value</th>
                    <th class="p-4 font-semibold text-right">Current Book Value</th>
                    <th class="p-4 font-semibold text-center">Depreciation</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4">
                        <a href="/assets/1" class="font-bold text-gray-900 group-hover:text-nour-primary transition-colors block">Delivery Truck - Isuzu 2024</a>
                        <span class="text-xs text-gray-500 font-mono">AST-VEH-0012</span>
                    </td>
                    <td class="p-4 text-gray-600">Vehicles</td>
                    <td class="p-4 text-right text-gray-500">$45,000.00</td>
                    <td class="p-4 text-right font-bold text-gray-900">$36,000.00</td>
                    <td class="p-4 text-center">
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded font-mono">Straight Line (5y)</span>
                    </td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-green-100 text-green-800 border border-green-200">Active</span>
                    </td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary p-1" title="View Schedule"><i class="fas fa-chart-line"></i></button>
                        <button class="text-gray-400 hover:text-blue-500 p-1 ml-1" title="Edit"><i class="far fa-edit"></i></button>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4">
                        <a href="/assets/2" class="font-bold text-gray-900 group-hover:text-nour-primary transition-colors block">Enterprise Server Cluster Rack</a>
                        <span class="text-xs text-gray-500 font-mono">AST-IT-0085</span>
                    </td>
                    <td class="p-4 text-gray-600">IT Equipment</td>
                    <td class="p-4 text-right text-gray-500">$120,000.00</td>
                    <td class="p-4 text-right font-bold text-gray-900">$10,000.00</td>
                    <td class="p-4 text-center">
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded font-mono">Declining Bal. (3y)</span>
                    </td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-yellow-100 text-yellow-800 border border-yellow-200">Fully Depreciated</span>
                    </td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary p-1" title="View Schedule"><i class="fas fa-chart-line"></i></button>
                        <button class="text-gray-400 hover:text-blue-500 p-1 ml-1" title="Edit"><i class="far fa-edit"></i></button>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors group opacity-75">
                    <td class="p-4">
                        <a href="/assets/3" class="font-bold text-gray-500 block line-through">Office Desk Sets (10x)</a>
                        <span class="text-xs text-gray-400 font-mono">AST-FNR-0005</span>
                    </td>
                    <td class="p-4 text-gray-500">Furniture & Fixtures</td>
                    <td class="p-4 text-right text-gray-400">$5,000.00</td>
                    <td class="p-4 text-right font-bold text-gray-400">$0.00</td>
                    <td class="p-4 text-center text-gray-400">-</td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-red-100 text-red-800 border border-red-200">Disposed</span>
                    </td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary p-1" title="View History"><i class="fas fa-history"></i></button>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>