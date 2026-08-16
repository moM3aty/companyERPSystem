<?php
// Path: resources/views/purchasing/suppliers/index.php
$pageTitle = 'Suppliers Directory';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Suppliers (Vendors)</h1>
        <p class="text-sm text-gray-500 mt-1">Manage vendor profiles, contacts, and account balances.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50"><i class="fas fa-file-import mr-1"></i> Import</button>
        <button class="btn-primary shadow-md"><i class="fas fa-plus mr-2"></i> Add Supplier</button>
    </div>
</div>

<div class="card p-0 overflow-visible relative">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex gap-4">
        <input type="text" placeholder="Search Supplier Name or Code..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-64">
    </div>
    <div class="table-responsive rounded-b-lg">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Code</th>
                    <th class="p-4 font-semibold">Supplier Name</th>
                    <th class="p-4 font-semibold">Contact Email</th>
                    <th class="p-4 font-semibold text-right">Account Balance (AP)</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 font-mono text-gray-500 text-xs">VND-001</td>
                    <td class="p-4 font-bold text-gray-900 group-hover:text-nour-primary cursor-pointer">Global Tech Suppliers Inc.</td>
                    <td class="p-4 text-gray-600">sales@globaltech.com</td>
                    <td class="p-4 text-right font-bold text-red-600">$14,500.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-[10px] uppercase font-bold">Active</span></td>
                    <td class="p-4 text-right"><button class="text-gray-400 hover:text-blue-500 px-2"><i class="far fa-edit"></i></button></td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 font-mono text-gray-500 text-xs">VND-002</td>
                    <td class="p-4 font-bold text-gray-900 group-hover:text-nour-primary cursor-pointer">Alpha Manufacturing</td>
                    <td class="p-4 text-gray-600">orders@alphamfg.com</td>
                    <td class="p-4 text-right font-bold text-gray-900">$0.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-[10px] uppercase font-bold">Active</span></td>
                    <td class="p-4 text-right"><button class="text-gray-400 hover:text-blue-500 px-2"><i class="far fa-edit"></i></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>