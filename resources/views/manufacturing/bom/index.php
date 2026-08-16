<?php
// Path: resources/views/manufacturing/bom/index.php
$pageTitle = 'Bill of Materials (BOM)';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span>Manufacturing</span></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Bill of Materials</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Bill of Materials (BOM)</h1>
        <p class="text-sm text-gray-500 mt-1">Define raw materials, components, and routing for manufactured products.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="/manufacturing/bom/create" class="btn-primary shadow-md flex items-center">
            <i class="fas fa-plus mr-2"></i> Create BOM
        </a>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex gap-4">
        <input type="text" placeholder="Search Finished Product..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-64">
        <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600">
            <option>All Types</option>
            <option>Manufacture this product</option>
            <option>Kit (Phantom BOM)</option>
        </select>
    </div>
    
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Finished Product</th>
                    <th class="p-4 font-semibold">BOM Ref. / Version</th>
                    <th class="p-4 font-semibold">BOM Type</th>
                    <th class="p-4 font-semibold text-center">Components</th>
                    <th class="p-4 font-semibold text-center">Routing (Ops)</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4">
                        <span class="font-bold text-gray-900 block">Custom Server Rack Unit - 42U</span>
                        <span class="text-xs text-gray-500 font-mono">SKU: FG-SRV-42U</span>
                    </td>
                    <td class="p-4 font-medium text-nour-primary">BOM-001 <span class="text-xs text-gray-400 ml-1">(v1.2)</span></td>
                    <td class="p-4"><span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Manufacture</span></td>
                    <td class="p-4 text-center font-bold text-gray-700">14 Items</td>
                    <td class="p-4 text-center font-bold text-gray-700">3 Ops</td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary px-2" title="Structure & Cost"><i class="fas fa-sitemap"></i></button>
                        <button class="text-gray-400 hover:text-blue-500 px-2" title="Edit"><i class="far fa-edit"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4">
                        <span class="font-bold text-gray-900 block">Office PC Bundle (Monitor + PC + Peripherals)</span>
                        <span class="text-xs text-gray-500 font-mono">SKU: BND-PC-01</span>
                    </td>
                    <td class="p-4 font-medium text-nour-primary">BOM-002 <span class="text-xs text-gray-400 ml-1">(v1.0)</span></td>
                    <td class="p-4"><span class="bg-purple-50 text-purple-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Kit</span></td>
                    <td class="p-4 text-center font-bold text-gray-700">4 Items</td>
                    <td class="p-4 text-center text-gray-300">-</td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-nour-primary px-2" title="Structure & Cost"><i class="fas fa-sitemap"></i></button>
                        <button class="text-gray-400 hover:text-blue-500 px-2" title="Edit"><i class="far fa-edit"></i></button>
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