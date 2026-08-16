<?php
// Path: resources/views/crm/customers.php
$pageTitle = 'Customers Management (CRM)';
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
                        <span>CRM</span>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-xs mx-2"></i>
                        <span class="text-gray-800 font-medium">Customers</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Customers Directory</h1>
        <p class="text-sm text-gray-500 mt-1">Manage your clients, leads, and communication history.</p>
    </div>
    
    <div class="mt-4 sm:mt-0 flex gap-3">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors flex items-center">
            <i class="fas fa-file-export mr-2 text-gray-400"></i> Export
        </button>
        <button class="btn-primary flex items-center shadow-md shadow-nour-primary/30">
            <i class="fas fa-plus mr-2"></i> Add New Customer
        </button>
    </div>
</div>

<!-- Filters and Search Section -->
<div class="card p-4 mb-6 bg-white">
    <form action="/crm/customers" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
        
        <!-- Search -->
        <div class="flex-1 w-full">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" placeholder="Search by name, email, or company..." 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50">
            </div>
        </div>

        <!-- Status Filter -->
        <div class="w-full md:w-48">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</label>
            <select name="status" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50 cursor-pointer">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="lead">Lead / Prospect</option>
            </select>
        </div>

        <!-- Industry Filter -->
        <div class="w-full md:w-48">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Industry</label>
            <select name="industry" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50 cursor-pointer">
                <option value="">All Industries</option>
                <option value="tech">Technology</option>
                <option value="manufacturing">Manufacturing</option>
                <option value="retail">Retail</option>
                <option value="healthcare">Healthcare</option>
            </select>
        </div>

        <!-- Filter Action -->
        <div class="w-full md:w-auto">
            <button type="submit" class="w-full md:w-auto bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md border border-gray-300 text-sm font-medium transition-colors">
                <i class="fas fa-filter mr-1"></i> Apply Filters
            </button>
        </div>
    </form>
</div>

<!-- Data Table Section -->
<div class="card p-0 overflow-visible z-10 relative"> <!-- overflow-visible allows dropdowns to show -->
    <div class="table-responsive rounded-t-lg">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold w-8">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </th>
                    <th class="p-4 font-semibold">Customer / Company</th>
                    <th class="p-4 font-semibold">Contact Info</th>
                    <th class="p-4 font-semibold">Total Sales</th>
                    <th class="p-4 font-semibold">Status</th>
                    <th class="p-4 font-semibold">Added Date</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                
                <!-- Row 1 -->
                <tr class="hover:bg-gray-50/80 transition-colors group">
                    <td class="p-4">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </td>
                    <td class="p-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-nour-primary flex items-center justify-center font-bold mr-3 flex-shrink-0">
                                AT
                            </div>
                            <div>
                                <a href="/crm/customers/1" class="font-semibold text-gray-900 group-hover:text-nour-primary transition-colors">Alpha Tech Solutions</a>
                                <p class="text-xs text-gray-500">Tech & Software</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <p class="font-medium">Sarah Jenkins</p>
                        <p class="text-xs text-gray-500"><i class="far fa-envelope mr-1"></i> sarah@alphatech.com</p>
                        <p class="text-xs text-gray-500 mt-0.5"><i class="fas fa-phone mr-1"></i> +1 (555) 123-4567</p>
                    </td>
                    <td class="p-4 font-medium text-gray-900">
                        $145,200.00
                    </td>
                    <td class="p-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span> Active
                        </span>
                    </td>
                    <td class="p-4 text-gray-500">
                        Oct 12, 2023
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button class="text-gray-400 hover:text-nour-primary transition-colors p-1" title="Send Email">
                                <i class="far fa-envelope"></i>
                            </button>
                            <a href="/crm/customers/1/edit" class="text-gray-400 hover:text-blue-500 transition-colors p-1" title="Edit">
                                <i class="far fa-edit"></i>
                            </a>
                            <button class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Delete">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 2 -->
                <tr class="hover:bg-gray-50/80 transition-colors group">
                    <td class="p-4">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </td>
                    <td class="p-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold mr-3 flex-shrink-0">
                                GP
                            </div>
                            <div>
                                <a href="/crm/customers/2" class="font-semibold text-gray-900 group-hover:text-nour-primary transition-colors">Global Pharma Group</a>
                                <p class="text-xs text-gray-500">Healthcare</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <p class="font-medium">Dr. Ahmed Yassin</p>
                        <p class="text-xs text-gray-500"><i class="far fa-envelope mr-1"></i> a.yassin@globalpharma.com</p>
                        <p class="text-xs text-gray-500 mt-0.5"><i class="fas fa-phone mr-1"></i> +971 50 123 4567</p>
                    </td>
                    <td class="p-4 font-medium text-gray-900">
                        $89,500.50
                    </td>
                    <td class="p-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span> Active
                        </span>
                    </td>
                    <td class="p-4 text-gray-500">
                        Nov 05, 2023
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button class="text-gray-400 hover:text-nour-primary transition-colors p-1" title="Send Email">
                                <i class="far fa-envelope"></i>
                            </button>
                            <a href="/crm/customers/2/edit" class="text-gray-400 hover:text-blue-500 transition-colors p-1" title="Edit">
                                <i class="far fa-edit"></i>
                            </a>
                            <button class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Delete">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 3 -->
                <tr class="hover:bg-gray-50/80 transition-colors group bg-orange-50/30">
                    <td class="p-4">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </td>
                    <td class="p-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center font-bold mr-3 flex-shrink-0">
                                RM
                            </div>
                            <div>
                                <a href="/crm/customers/3" class="font-semibold text-gray-900 group-hover:text-nour-primary transition-colors">Royal Manufacturing</a>
                                <p class="text-xs text-gray-500">Manufacturing</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <p class="font-medium">Michael Chen</p>
                        <p class="text-xs text-gray-500"><i class="far fa-envelope mr-1"></i> m.chen@royalman.com</p>
                        <p class="text-xs text-gray-500 mt-0.5"><i class="fas fa-phone mr-1"></i> +44 20 7123 4567</p>
                    </td>
                    <td class="p-4 font-medium text-gray-900">
                        $0.00
                    </td>
                    <td class="p-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            <span class="w-1.5 h-1.5 bg-orange-500 rounded-full mr-1.5"></span> Lead
                        </span>
                    </td>
                    <td class="p-4 text-gray-500">
                        Jan 10, 2024
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button class="text-gray-400 hover:text-nour-primary transition-colors p-1" title="Send Email">
                                <i class="far fa-envelope"></i>
                            </button>
                            <a href="/crm/customers/3/edit" class="text-gray-400 hover:text-blue-500 transition-colors p-1" title="Edit">
                                <i class="far fa-edit"></i>
                            </a>
                            <button class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Delete">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                 <!-- Row 4 -->
                 <tr class="hover:bg-gray-50/80 transition-colors group text-gray-400">
                    <td class="p-4">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </td>
                    <td class="p-4">
                        <div class="flex items-center opacity-60">
                            <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold mr-3 flex-shrink-0">
                                SF
                            </div>
                            <div>
                                <a href="/crm/customers/4" class="font-semibold group-hover:text-nour-primary transition-colors">Style & Fashion Retail</a>
                                <p class="text-xs">Retail</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 opacity-70">
                        <p class="font-medium">Fatima Ali</p>
                        <p class="text-xs"><i class="far fa-envelope mr-1"></i> info@styleretail.com</p>
                    </td>
                    <td class="p-4 font-medium opacity-70">
                        $12,400.00
                    </td>
                    <td class="p-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span> Inactive
                        </span>
                    </td>
                    <td class="p-4 opacity-70">
                        Mar 22, 2022
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="/crm/customers/4/edit" class="text-gray-300 hover:text-blue-500 transition-colors p-1" title="Edit">
                                <i class="far fa-edit"></i>
                            </a>
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
                        Showing <span class="font-medium text-gray-900">1</span> to <span class="font-medium text-gray-900">4</span> of <span class="font-medium text-gray-900">97</span> results
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
                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium"> 10 </a>
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