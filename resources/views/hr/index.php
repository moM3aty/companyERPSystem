<?php
// Path: resources/views/hr/index.php
$pageTitle = 'Human Resources Dashboard';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Human Resources (HRIS)</h1>
        <p class="text-sm text-gray-500 mt-1">Manage personnel, attendance, payroll, and recruitment.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <a href="/hr/employees" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-users mr-2 text-gray-400"></i> Employees Directory
        </a>
    </div>
</div>

<!-- HR KPIs -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="card p-5 border-l-4 border-l-nour-primary">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Employees</p>
        <h3 class="text-2xl font-black text-gray-900">142</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">Across 3 branches</p>
    </div>
    <div class="card p-5 border-l-4 border-l-yellow-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">On Leave Today</p>
        <h3 class="text-2xl font-black text-gray-900">7</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">Approved vacations</p>
    </div>
    <div class="card p-5 border-l-4 border-l-green-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Open Positions</p>
        <h3 class="text-2xl font-black text-gray-900">4</h3>
        <p class="text-xs text-green-600 mt-2 font-medium">Actively recruiting</p>
    </div>
    <div class="card p-5 border-l-4 border-l-purple-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Payroll (MTD)</p>
        <h3 class="text-2xl font-black text-gray-900">$185.4K</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">Estimated run cost</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Pending Leave Requests -->
    <div class="card p-0 overflow-hidden flex flex-col">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800"><i class="fas fa-plane-departure text-yellow-500 mr-2"></i> Leave Requests Pending</h3>
        </div>
        <div class="table-responsive">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <tbody class="text-sm divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name=Omar+Khalid&background=005eb8&color=fff" class="w-8 h-8 rounded-full">
                                <div>
                                    <span class="font-bold text-gray-900 block">Omar Khalid</span>
                                    <span class="text-[10px] text-gray-500">Annual Leave (5 Days)</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-center">
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Pending</span>
                        </td>
                        <td class="p-4 text-right">
                            <button class="text-xs text-green-600 hover:underline font-bold mr-3">Approve</button>
                            <button class="text-xs text-red-600 hover:underline font-bold">Reject</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name=Sara+Ali&background=f59e0b&color=fff" class="w-8 h-8 rounded-full">
                                <div>
                                    <span class="font-bold text-gray-900 block">Sara Ali</span>
                                    <span class="text-[10px] text-gray-500">Sick Leave (2 Days)</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-center">
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Pending</span>
                        </td>
                        <td class="p-4 text-right">
                            <button class="text-xs text-green-600 hover:underline font-bold mr-3">Approve</button>
                            <button class="text-xs text-red-600 hover:underline font-bold">Reject</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Shortcuts -->
    <div class="card p-5">
        <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Quick Actions</h3>
        <div class="grid grid-cols-2 gap-4">
            <a href="/hr/payroll/dashboard" class="p-4 border border-gray-200 rounded-lg hover:border-nour-primary hover:shadow-md transition-all text-center group">
                <i class="fas fa-money-check-alt text-3xl text-gray-300 group-hover:text-nour-primary mb-2 transition-colors"></i>
                <p class="font-bold text-sm text-gray-800">Run Payroll</p>
            </a>
            <a href="/hr/attendance" class="p-4 border border-gray-200 rounded-lg hover:border-nour-primary hover:shadow-md transition-all text-center group">
                <i class="fas fa-fingerprint text-3xl text-gray-300 group-hover:text-nour-primary mb-2 transition-colors"></i>
                <p class="font-bold text-sm text-gray-800">Attendance Log</p>
            </a>
            <a href="/hr/recruitment" class="p-4 border border-gray-200 rounded-lg hover:border-nour-primary hover:shadow-md transition-all text-center group">
                <i class="fas fa-user-tie text-3xl text-gray-300 group-hover:text-nour-primary mb-2 transition-colors"></i>
                <p class="font-bold text-sm text-gray-800">Recruitment</p>
            </a>
            <a href="/hr/contracts" class="p-4 border border-gray-200 rounded-lg hover:border-nour-primary hover:shadow-md transition-all text-center group">
                <i class="fas fa-file-contract text-3xl text-gray-300 group-hover:text-nour-primary mb-2 transition-colors"></i>
                <p class="font-bold text-sm text-gray-800">Contracts</p>
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>