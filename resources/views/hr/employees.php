<?php
// Path: resources/views/hr/employees.php
$pageTitle = 'Employees Directory';
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
                        <span>Human Resources</span>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-xs mx-2"></i>
                        <span class="text-gray-800 font-medium">Employees</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Employees Directory</h1>
        <p class="text-sm text-gray-500 mt-1">Manage personnel records, organizational structure, and contracts.</p>
    </div>
    
    <div class="mt-4 sm:mt-0 flex gap-3">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors flex items-center">
            <i class="fas fa-sitemap mr-2 text-gray-400"></i> Org Chart
        </button>
        <button class="btn-primary flex items-center shadow-md shadow-nour-primary/30">
            <i class="fas fa-user-plus mr-2"></i> Add Employee
        </button>
    </div>
</div>

<!-- Stats / Overview -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="card p-5 border-l-4 border-l-nour-primary flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Headcount</p>
            <h3 class="text-2xl font-bold text-gray-900">142</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-nour-primary">
            <i class="fas fa-users"></i>
        </div>
    </div>
    <div class="card p-5 border-l-4 border-l-green-500 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">On Duty</p>
            <h3 class="text-2xl font-bold text-gray-900">135</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-500">
            <i class="fas fa-user-check"></i>
        </div>
    </div>
    <div class="card p-5 border-l-4 border-l-yellow-500 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">On Leave / Vacation</p>
            <h3 class="text-2xl font-bold text-gray-900">7</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-500">
            <i class="fas fa-plane-departure"></i>
        </div>
    </div>
    <div class="card p-5 border-l-4 border-l-red-500 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Contract Expiring (30d)</p>
            <h3 class="text-2xl font-bold text-red-600">3</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-500">
            <i class="fas fa-file-contract"></i>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card p-4 mb-6 bg-white">
    <form action="/hr/employees" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
        <div class="flex-1 w-full">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Search Employee</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" placeholder="Name, ID, or Email..." 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50">
            </div>
        </div>
        <div class="w-full md:w-48">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Department</label>
            <select name="department" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50 cursor-pointer">
                <option value="">All Departments</option>
                <option value="it">Information Technology</option>
                <option value="sales">Sales & Marketing</option>
                <option value="finance">Finance & Accounting</option>
                <option value="hr">Human Resources</option>
                <option value="operations">Operations & Logistics</option>
            </select>
        </div>
        <div class="w-full md:w-48">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</label>
            <select name="status" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50 cursor-pointer">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="leave">On Leave</option>
                <option value="suspended">Suspended</option>
                <option value="terminated">Terminated</option>
            </select>
        </div>
        <div class="w-full md:w-auto">
            <button type="submit" class="w-full md:w-auto bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md border border-gray-300 text-sm font-medium transition-colors">
                Apply
            </button>
        </div>
    </form>
</div>

<!-- Employee Directory Table -->
<div class="card p-0 overflow-visible z-10 relative">
    <div class="table-responsive rounded-t-lg">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold w-8">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </th>
                    <th class="p-4 font-semibold">Employee</th>
                    <th class="p-4 font-semibold">Department & Role</th>
                    <th class="p-4 font-semibold">Contact</th>
                    <th class="p-4 font-semibold">Join Date</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                
                <!-- Employee 1 -->
                <tr class="hover:bg-gray-50/80 transition-colors group">
                    <td class="p-4"><input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary"></td>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=Ahmed+Hassan&background=005eb8&color=fff" alt="Avatar" class="w-10 h-10 rounded-full border border-gray-200">
                            <div>
                                <a href="/hr/employees/EMP-001" class="font-bold text-gray-900 group-hover:text-nour-primary transition-colors">Ahmed Hassan</a>
                                <p class="text-xs text-gray-500 font-mono mt-0.5">EMP-2022-045</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <p class="font-semibold text-gray-800">Senior Software Engineer</p>
                        <p class="text-xs text-gray-500 mt-0.5">Information Technology</p>
                    </td>
                    <td class="p-4">
                        <p class="text-xs text-gray-600"><i class="far fa-envelope w-4 text-gray-400"></i> a.hassan@nourtrust.com</p>
                        <p class="text-xs text-gray-600 mt-1"><i class="fas fa-phone w-4 text-gray-400"></i> +966 50 123 4567</p>
                    </td>
                    <td class="p-4 text-gray-600">Mar 15, 2022</td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                            Active
                        </span>
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button class="text-gray-400 hover:text-nour-primary transition-colors p-1" title="View Profile"><i class="far fa-eye"></i></button>
                            <button class="text-gray-400 hover:text-blue-500 transition-colors p-1" title="Edit"><i class="far fa-edit"></i></button>
                            <button class="text-gray-400 hover:text-gray-700 transition-colors p-1 dropdown-menu-trigger"><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                    </td>
                </tr>

                <!-- Employee 2 -->
                <tr class="hover:bg-yellow-50/30 transition-colors group bg-yellow-50/10">
                    <td class="p-4"><input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary"></td>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=Sarah+Smith&background=f59e0b&color=fff" alt="Avatar" class="w-10 h-10 rounded-full border border-gray-200">
                            <div>
                                <a href="/hr/employees/EMP-002" class="font-bold text-gray-900 group-hover:text-nour-primary transition-colors">Sarah Smith</a>
                                <p class="text-xs text-gray-500 font-mono mt-0.5">EMP-2023-112</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <p class="font-semibold text-gray-800">Financial Controller</p>
                        <p class="text-xs text-gray-500 mt-0.5">Finance & Accounting</p>
                    </td>
                    <td class="p-4">
                        <p class="text-xs text-gray-600"><i class="far fa-envelope w-4 text-gray-400"></i> s.smith@nourtrust.com</p>
                        <p class="text-xs text-gray-600 mt-1"><i class="fas fa-phone w-4 text-gray-400"></i> +971 55 987 6543</p>
                    </td>
                    <td class="p-4 text-gray-600">Jan 10, 2023</td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                            On Leave
                        </span>
                        <p class="text-[10px] text-gray-500 mt-0.5">Returns: Aug 25</p>
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button class="text-gray-400 hover:text-nour-primary transition-colors p-1" title="View Profile"><i class="far fa-eye"></i></button>
                            <button class="text-gray-400 hover:text-blue-500 transition-colors p-1" title="Edit"><i class="far fa-edit"></i></button>
                        </div>
                    </td>
                </tr>

                <!-- Employee 3 -->
                <tr class="hover:bg-gray-50/80 transition-colors group">
                    <td class="p-4"><input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary"></td>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=Omar+Khalid&background=005eb8&color=fff" alt="Avatar" class="w-10 h-10 rounded-full border border-gray-200">
                            <div>
                                <a href="/hr/employees/EMP-003" class="font-bold text-gray-900 group-hover:text-nour-primary transition-colors">Omar Khalid</a>
                                <p class="text-xs text-gray-500 font-mono mt-0.5">EMP-2024-005</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <p class="font-semibold text-gray-800">Sales Manager</p>
                        <p class="text-xs text-gray-500 mt-0.5">Sales & Marketing</p>
                    </td>
                    <td class="p-4">
                        <p class="text-xs text-gray-600"><i class="far fa-envelope w-4 text-gray-400"></i> o.khalid@nourtrust.com</p>
                        <p class="text-xs text-gray-600 mt-1"><i class="fas fa-phone w-4 text-gray-400"></i> +966 54 321 0987</p>
                    </td>
                    <td class="p-4 text-gray-600">Feb 01, 2024</td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                            Active
                        </span>
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button class="text-gray-400 hover:text-nour-primary transition-colors p-1" title="View Profile"><i class="far fa-eye"></i></button>
                            <button class="text-gray-400 hover:text-blue-500 transition-colors p-1" title="Edit"><i class="far fa-edit"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 rounded-b-lg flex items-center justify-between">
        <p class="text-sm text-gray-700">Showing <span class="font-medium text-gray-900">1</span> to <span class="font-medium text-gray-900">3</span> of <span class="font-medium text-gray-900">142</span> employees</p>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"><i class="fas fa-chevron-left text-xs"></i></a>
            <a href="#" class="z-10 bg-nour-primary/10 border-nour-primary text-nour-primary relative inline-flex items-center px-4 py-2 border text-sm font-medium">1</a>
            <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">2</a>
            <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">3</a>
            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"><i class="fas fa-chevron-right text-xs"></i></a>
        </nav>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>