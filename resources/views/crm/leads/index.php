<?php
// Path: resources/views/crm/leads/index.php
$pageTitle = 'Leads Management';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><a href="/crm/customers" class="hover:text-nour-primary transition-colors">CRM</a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Leads</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Leads & Prospects</h1>
        <p class="text-sm text-gray-500 mt-1">Manage potential customers before they become active accounts or opportunities.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50"><i class="fas fa-cloud-download-alt mr-1"></i> Import Leads</button>
        <button class="btn-primary shadow-md"><i class="fas fa-plus mr-2"></i> Add Lead</button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="card p-4 border-l-4 border-l-gray-400">
        <p class="text-xs font-semibold text-gray-500 uppercase">New Leads</p>
        <h3 class="text-2xl font-bold text-gray-900 mt-1">45</h3>
    </div>
    <div class="card p-4 border-l-4 border-l-blue-500">
        <p class="text-xs font-semibold text-gray-500 uppercase">Contacted</p>
        <h3 class="text-2xl font-bold text-gray-900 mt-1">28</h3>
    </div>
    <div class="card p-4 border-l-4 border-l-yellow-500">
        <p class="text-xs font-semibold text-gray-500 uppercase">Qualified</p>
        <h3 class="text-2xl font-bold text-gray-900 mt-1">12</h3>
    </div>
    <div class="card p-4 border-l-4 border-l-green-500">
        <p class="text-xs font-semibold text-gray-500 uppercase">Converted (YTD)</p>
        <h3 class="text-2xl font-bold text-gray-900 mt-1">104</h3>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex gap-4">
        <input type="text" placeholder="Search by name, company, or email..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary flex-1 max-w-md">
        <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600">
            <option>All Statuses</option>
            <option>New</option>
            <option>Contacted</option>
            <option>Qualified</option>
            <option>Lost/Junk</option>
        </select>
    </div>
    
    <div class="table-responsive rounded-b-lg">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Lead Name / Company</th>
                    <th class="p-4 font-semibold">Contact Info</th>
                    <th class="p-4 font-semibold">Source</th>
                    <th class="p-4 font-semibold">Assigned To</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4">
                        <span class="font-bold text-gray-900 block">Mohamed Tariq</span>
                        <span class="text-xs text-gray-500">Future Builders LLC</span>
                    </td>
                    <td class="p-4">
                        <p class="text-xs text-gray-600"><i class="far fa-envelope w-4"></i> m.tariq@futureb.com</p>
                        <p class="text-xs text-gray-600 mt-0.5"><i class="fas fa-phone w-4"></i> +966 55 123 9876</p>
                    </td>
                    <td class="p-4 text-gray-600"><span class="bg-gray-100 px-2 py-0.5 rounded text-xs">Website Form</span></td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Omar+Khalid&background=005eb8&color=fff" class="w-6 h-6 rounded-full">
                            <span class="text-xs font-medium text-gray-700">Omar Khalid</span>
                        </div>
                    </td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-gray-200 text-gray-800 rounded text-[10px] uppercase font-bold">New</span></td>
                    <td class="p-4 text-right">
                        <button class="bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 px-3 py-1 rounded text-xs font-bold transition-colors shadow-sm mr-2" title="Convert to Customer & Opportunity">Convert</button>
                        <button class="text-gray-400 hover:text-nour-primary px-1"><i class="far fa-edit"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4">
                        <span class="font-bold text-gray-900 block">Sara Youssef</span>
                        <span class="text-xs text-gray-500">Skyline Retail</span>
                    </td>
                    <td class="p-4">
                        <p class="text-xs text-gray-600"><i class="far fa-envelope w-4"></i> sara@skylineretail.ae</p>
                        <p class="text-xs text-gray-600 mt-0.5"><i class="fas fa-phone w-4"></i> +971 50 456 7890</p>
                    </td>
                    <td class="p-4 text-gray-600"><span class="bg-gray-100 px-2 py-0.5 rounded text-xs">Trade Show</span></td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Sarah+Smith&background=f59e0b&color=fff" class="w-6 h-6 rounded-full">
                            <span class="text-xs font-medium text-gray-700">Sarah Smith</span>
                        </div>
                    </td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded text-[10px] uppercase font-bold">Qualified</span></td>
                    <td class="p-4 text-right">
                        <button class="bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 px-3 py-1 rounded text-xs font-bold transition-colors shadow-sm mr-2" title="Convert to Customer & Opportunity">Convert</button>
                        <button class="text-gray-400 hover:text-nour-primary px-1"><i class="far fa-edit"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>