<?php
// Path: resources/views/admin/roles.php
$pageTitle = 'Roles & Permissions';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/admin/users" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Users</a>
        <h1 class="text-2xl font-bold text-gray-800">Roles & Access Control</h1>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md flex items-center">
            <i class="fas fa-plus mr-2"></i> Create Custom Role
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Roles List Sidebar -->
    <div class="lg:col-span-1 space-y-3">
        <div class="bg-nour-dark text-white p-4 rounded-lg shadow-md cursor-pointer border-l-4 border-l-nour-light">
            <h3 class="font-bold text-base">Finance Manager</h3>
            <p class="text-xs text-gray-400 mt-1">Full access to Accounting & Reports. 5 Users.</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:border-nour-primary transition-colors text-gray-700">
            <h3 class="font-bold text-base">Sales Representative</h3>
            <p class="text-xs text-gray-500 mt-1">Read/Write CRM & Quotations. 12 Users.</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:border-nour-primary transition-colors text-gray-700">
            <h3 class="font-bold text-base">Warehouse Staff</h3>
            <p class="text-xs text-gray-500 mt-1">Read/Write Inventory. 8 Users.</p>
        </div>
    </div>

    <!-- Permission Matrix -->
    <div class="lg:col-span-3 card p-0 overflow-hidden flex flex-col h-full">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Permissions for <span class="text-nour-primary">Finance Manager</span></h3>
            <button class="text-sm font-medium text-nour-primary bg-nour-primary/10 px-3 py-1.5 rounded hover:bg-nour-primary hover:text-white transition-colors">Save Changes</button>
        </div>
        
        <div class="overflow-y-auto max-h-[600px] custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead class="sticky top-0 bg-white z-10 shadow-sm">
                    <tr class="text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                        <th class="p-4 font-semibold w-1/3">Module / Feature</th>
                        <th class="p-4 font-semibold text-center w-16">Read</th>
                        <th class="p-4 font-semibold text-center w-16">Create</th>
                        <th class="p-4 font-semibold text-center w-16">Edit</th>
                        <th class="p-4 font-semibold text-center w-16">Delete</th>
                        <th class="p-4 font-semibold text-center w-16">Approve</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    
                    <!-- Accounting Section -->
                    <tr class="bg-gray-50 border-y border-gray-200">
                        <td class="px-4 py-2 font-bold text-gray-800" colspan="6"><i class="fas fa-file-invoice-dollar w-5 text-gray-400"></i> Accounting & Finance</td>
                    </tr>
                    <tr class="hover:bg-blue-50/20">
                        <td class="p-4 font-medium text-gray-700 pl-8">Journal Entries</td>
                        <td class="p-4 text-center"><input type="checkbox" checked class="rounded text-nour-primary w-4 h-4 cursor-pointer"></td>
                        <td class="p-4 text-center"><input type="checkbox" checked class="rounded text-nour-primary w-4 h-4 cursor-pointer"></td>
                        <td class="p-4 text-center"><input type="checkbox" checked class="rounded text-nour-primary w-4 h-4 cursor-pointer"></td>
                        <td class="p-4 text-center"><input type="checkbox" class="rounded text-nour-primary w-4 h-4 cursor-pointer"></td>
                        <td class="p-4 text-center"><input type="checkbox" checked class="rounded text-green-500 focus:ring-green-500 w-4 h-4 cursor-pointer"></td>
                    </tr>
                    <tr class="hover:bg-blue-50/20">
                        <td class="p-4 font-medium text-gray-700 pl-8">Chart of Accounts</td>
                        <td class="p-4 text-center"><input type="checkbox" checked class="rounded text-nour-primary w-4 h-4 cursor-pointer"></td>
                        <td class="p-4 text-center"><input type="checkbox" checked class="rounded text-nour-primary w-4 h-4 cursor-pointer"></td>
                        <td class="p-4 text-center"><input type="checkbox" checked class="rounded text-nour-primary w-4 h-4 cursor-pointer"></td>
                        <td class="p-4 text-center"><input type="checkbox" class="rounded text-nour-primary w-4 h-4 cursor-pointer"></td>
                        <td class="p-4 text-center text-gray-300">-</td>
                    </tr>

                    <!-- Sales Section -->
                    <tr class="bg-gray-50 border-y border-gray-200">
                        <td class="px-4 py-2 font-bold text-gray-800" colspan="6"><i class="fas fa-hand-holding-dollar w-5 text-gray-400"></i> Sales & CRM</td>
                    </tr>
                    <tr class="hover:bg-blue-50/20">
                        <td class="p-4 font-medium text-gray-700 pl-8">Invoices (AR)</td>
                        <td class="p-4 text-center"><input type="checkbox" checked class="rounded text-nour-primary w-4 h-4 cursor-pointer"></td>
                        <td class="p-4 text-center"><input type="checkbox" class="rounded text-nour-primary w-4 h-4 cursor-pointer"></td>
                        <td class="p-4 text-center"><input type="checkbox" class="rounded text-nour-primary w-4 h-4 cursor-pointer"></td>
                        <td class="p-4 text-center"><input type="checkbox" class="rounded text-nour-primary w-4 h-4 cursor-pointer"></td>
                        <td class="p-4 text-center text-gray-300">-</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>