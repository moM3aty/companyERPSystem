<?php
// Path: resources/views/enterprise/advanced-hr/performance.php
$pageTitle = 'Performance Appraisals';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Performance & Appraisals</h1>
        <p class="text-sm text-gray-500 mt-1">Manage OKRs, KPIs, and conduct periodic employee evaluations.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-bullseye mr-2 text-red-500"></i> Corporate OKRs
        </button>
        <button class="btn-primary shadow-md">
            <i class="fas fa-star mr-2"></i> Start Appraisal Cycle
        </button>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <h3 class="font-bold text-gray-800">Current Cycle: Mid-Year Review 2026</h3>
        <span class="text-xs bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full font-bold">Self-Assessments Phase</span>
    </div>
    
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Employee</th>
                    <th class="p-4 font-semibold">Department / Role</th>
                    <th class="p-4 font-semibold">Direct Manager</th>
                    <th class="p-4 font-semibold text-center">Completion</th>
                    <th class="p-4 font-semibold text-center">Score</th>
                    <th class="p-4 font-semibold text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=Ahmed+Hassan&background=005eb8&color=fff" class="w-8 h-8 rounded-full border border-gray-200">
                            <span class="font-bold text-gray-900 group-hover:text-nour-primary transition-colors cursor-pointer">Ahmed Hassan</span>
                        </div>
                    </td>
                    <td class="p-4 text-gray-500">IT / Senior Dev</td>
                    <td class="p-4 font-medium text-gray-800">Sarah Smith</td>
                    <td class="p-4 text-center">
                        <div class="w-24 mx-auto bg-gray-200 rounded-full h-1.5 mt-1">
                            <div class="bg-green-500 h-1.5 rounded-full" style="width: 100%"></div>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">Manager Reviewed</p>
                    </td>
                    <td class="p-4 text-center">
                        <span class="font-black text-green-600 text-base">4.8</span><span class="text-xs text-gray-400">/5</span>
                    </td>
                    <td class="p-4 text-right">
                        <button class="text-nour-primary hover:text-nour-dark text-xs font-medium border border-nour-primary/30 px-2 py-1 rounded bg-nour-primary/5 transition-colors">View Report</button>
                    </td>
                </tr>
                
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=Omar+Khalid&background=f59e0b&color=fff" class="w-8 h-8 rounded-full border border-gray-200">
                            <span class="font-bold text-gray-900 group-hover:text-nour-primary transition-colors cursor-pointer">Omar Khalid</span>
                        </div>
                    </td>
                    <td class="p-4 text-gray-500">Sales / Exec</td>
                    <td class="p-4 font-medium text-gray-800">John Doe</td>
                    <td class="p-4 text-center">
                        <div class="w-24 mx-auto bg-gray-200 rounded-full h-1.5 mt-1">
                            <div class="bg-yellow-400 h-1.5 rounded-full" style="width: 50%"></div>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">Self Done. Pending Manager</p>
                    </td>
                    <td class="p-4 text-center text-gray-300">-</td>
                    <td class="p-4 text-right">
                        <button class="text-yellow-600 hover:text-yellow-800 text-xs font-medium px-2 py-1">Nudge Manager</button>
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