<?php
// Path: resources/views/dashboard/index.php
$pageTitle = 'Enterprise Dashboard';
ob_start();
?>

<div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Enterprise Overview</h1>
        <p class="text-sm text-slate-500 mt-1 font-medium">Real-time insights across all departments for <span class="text-nour-primary font-bold">August 2026</span>.</p>
    </div>
    <div class="flex gap-3">
        <button class="bg-white border border-slate-200 text-slate-700 px-4 py-2.5 rounded-lg shadow-sm text-sm font-bold hover:bg-slate-50 transition-all flex items-center">
            <i class="far fa-calendar-alt mr-2 text-slate-400"></i> This Month <i class="fas fa-chevron-down ml-2 text-[10px] text-slate-400"></i>
        </button>
        <button class="btn-primary shadow-lg shadow-blue-500/30">
            <i class="fas fa-cloud-download-alt mr-2"></i> Export Report
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Revenue -->
    <div class="card p-6 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-50 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 text-green-600 flex items-center justify-center text-xl shadow-sm">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded bg-green-50 text-green-700 text-xs font-bold border border-green-100">
                    <i class="fas fa-arrow-up mr-1 text-[10px]"></i> 14.5%
                </span>
            </div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Revenue</p>
            <h3 class="text-3xl font-black text-slate-900">$1,245,600</h3>
        </div>
    </div>

    <!-- Operating Costs -->
    <div class="card p-6 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-red-50 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-xl shadow-sm">
                    <i class="fas fa-chart-line"></i>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded bg-red-50 text-red-700 text-xs font-bold border border-red-100">
                    <i class="fas fa-arrow-down mr-1 text-[10px]"></i> 2.1%
                </span>
            </div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Operating Costs</p>
            <h3 class="text-3xl font-black text-slate-900">$485,200</h3>
        </div>
    </div>

    <!-- Cash & Bank -->
    <div class="card p-6 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl shadow-sm">
                    <i class="fas fa-university"></i>
                </div>
                <span class="text-xs font-bold text-slate-400 mt-1">Live Balance</span>
            </div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Liquidity</p>
            <h3 class="text-3xl font-black text-slate-900">$2.1M</h3>
        </div>
    </div>

    <!-- Active Projects / Headcount -->
    <div class="card p-6 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-50 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl shadow-sm">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded bg-purple-50 text-purple-700 text-xs font-bold border border-purple-100">
                    3 New
                </span>
            </div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Active Projects</p>
            <h3 class="text-3xl font-black text-slate-900">45</h3>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
    
    <!-- Big Chart Area -->
    <div class="card p-0 col-span-1 xl:col-span-2 flex flex-col">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Revenue vs Expenses</h3>
                <p class="text-xs text-slate-500 mt-1">YTD Financial Performance</p>
            </div>
            <button class="text-slate-400 hover:text-nour-primary transition-colors p-2"><i class="fas fa-ellipsis-v"></i></button>
        </div>
        
        <!-- UI Magic: Simulated Modern Chart using CSS -->
        <div class="flex-1 p-6 relative min-h-[320px] flex items-end justify-between gap-4">
            <!-- Grid lines -->
            <div class="absolute inset-0 p-6 flex flex-col justify-between z-0 pointer-events-none">
                <div class="w-full border-b border-slate-100 border-dashed h-0"></div>
                <div class="w-full border-b border-slate-100 border-dashed h-0"></div>
                <div class="w-full border-b border-slate-100 border-dashed h-0"></div>
                <div class="w-full border-b border-slate-100 border-dashed h-0"></div>
                <div class="w-full border-b border-slate-200 h-0"></div>
            </div>
            
            <!-- Bars (Jan - Jun) -->
            <?php 
                $data = [
                    ['m' => 'Jan', 'r' => 40, 'e' => 30],
                    ['m' => 'Feb', 'r' => 55, 'e' => 35],
                    ['m' => 'Mar', 'r' => 45, 'e' => 40],
                    ['m' => 'Apr', 'r' => 70, 'e' => 45],
                    ['m' => 'May', 'r' => 65, 'e' => 50],
                    ['m' => 'Jun', 'r' => 90, 'e' => 55],
                ];
                foreach($data as $col):
            ?>
            <div class="flex-1 flex justify-center gap-1.5 z-10 group relative h-full items-end">
                <div class="w-1/3 bg-slate-200 rounded-t-md hover:bg-slate-300 transition-colors" style="height: <?= $col['e'] ?>%"></div>
                <div class="w-1/3 bg-blue-500 rounded-t-md hover:bg-blue-600 transition-colors shadow-sm" style="height: <?= $col['r'] ?>%"></div>
                <!-- Tooltip -->
                <div class="absolute -top-10 bg-slate-800 text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Rev: $<?= $col['r'] ?>k</div>
                <!-- Label -->
                <span class="absolute -bottom-6 text-xs font-bold text-slate-400"><?= $col['m'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Legend -->
        <div class="px-6 pb-6 pt-4 flex gap-6 border-t border-slate-50 mt-4">
            <div class="flex items-center text-xs font-bold text-slate-600"><span class="w-3 h-3 rounded bg-blue-500 mr-2"></span> Revenue</div>
            <div class="flex items-center text-xs font-bold text-slate-600"><span class="w-3 h-3 rounded bg-slate-200 mr-2"></span> Expenses</div>
        </div>
    </div>

    <!-- Right Column: Approvals & Tasks -->
    <div class="col-span-1 space-y-6">
        
        <!-- Approvals Widget -->
        <div class="card p-0 flex flex-col h-full">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center rounded-t-xl">
                <h3 class="font-bold text-slate-800"><i class="fas fa-inbox text-nour-primary mr-2"></i> Pending Approvals</h3>
                <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded-full">3 New</span>
            </div>
            <div class="flex-1 p-0">
                <ul class="divide-y divide-slate-100">
                    <li class="p-4 hover:bg-slate-50 transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Purchase Order</span>
                            <span class="text-xs text-slate-400 font-medium">10 mins ago</span>
                        </div>
                        <p class="text-sm font-bold text-slate-800 mb-1">PO-2608-012 (IT Equipment)</p>
                        <p class="text-xs text-slate-500 mb-3">Requested by <span class="font-semibold text-slate-700">John Doe</span> &bull; <span class="font-bold text-slate-900">$45,000</span></p>
                        <div class="flex gap-2">
                            <button class="flex-1 bg-green-50 hover:bg-green-100 text-green-700 border border-green-200 py-1.5 rounded text-xs font-bold transition-colors">Approve</button>
                            <button class="flex-1 bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 py-1.5 rounded text-xs font-bold transition-colors">Review</button>
                        </div>
                    </li>
                    <li class="p-4 hover:bg-slate-50 transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded">Leave Request</span>
                            <span class="text-xs text-slate-400 font-medium">2 hrs ago</span>
                        </div>
                        <p class="text-sm font-bold text-slate-800 mb-1">Annual Leave (5 Days)</p>
                        <p class="text-xs text-slate-500 mb-3">Requested by <span class="font-semibold text-slate-700">Sarah Smith</span></p>
                        <div class="flex gap-2">
                            <button class="flex-1 bg-green-50 hover:bg-green-100 text-green-700 border border-green-200 py-1.5 rounded text-xs font-bold transition-colors">Approve</button>
                            <button class="flex-1 bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 py-1.5 rounded text-xs font-bold transition-colors">Review</button>
                        </div>
                    </li>
                </ul>
            </div>
            <a href="#" class="block p-3 text-center text-xs font-bold text-nour-primary border-t border-slate-100 hover:bg-slate-50 transition-colors rounded-b-xl">
                View All Pending Tasks <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-white">
        <h3 class="text-lg font-bold text-slate-800">Latest Financial Transactions</h3>
        <div class="relative hidden sm:block">
            <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            <input type="text" placeholder="Search transactions..." class="pl-8 pr-3 py-1.5 text-sm border border-slate-200 rounded-md focus:ring-nour-primary focus:border-nour-primary">
        </div>
    </div>
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr>
                    <th>Ref ID</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th class="text-right">Amount</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="font-bold text-nour-primary">JE-2026-0815</td>
                    <td class="text-slate-500">Aug 15, 2026</td>
                    <td class="font-medium text-slate-900">Sales Invoice Recognition (Alpha Tech)</td>
                    <td><span class="badge bg-blue-50 text-blue-700 border border-blue-200">Income</span></td>
                    <td class="text-right font-black text-slate-900">$28,175.00</td>
                    <td class="text-center"><span class="text-green-500 font-bold text-xs"><i class="fas fa-check-circle mr-1"></i> Posted</span></td>
                </tr>
                <tr>
                    <td class="font-bold text-nour-primary">PV-2026-0814</td>
                    <td class="text-slate-500">Aug 14, 2026</td>
                    <td class="font-medium text-slate-900">Vendor Payment (Global IT)</td>
                    <td><span class="badge bg-red-50 text-red-700 border border-red-200">Expense</span></td>
                    <td class="text-right font-black text-slate-900">$12,400.00</td>
                    <td class="text-center"><span class="text-green-500 font-bold text-xs"><i class="fas fa-check-circle mr-1"></i> Posted</span></td>
                </tr>
                <tr>
                    <td class="font-bold text-slate-500">PR-2026-0810</td>
                    <td class="text-slate-500">Aug 10, 2026</td>
                    <td class="font-medium text-slate-900">Monthly Payroll Accrual</td>
                    <td><span class="badge bg-slate-100 text-slate-700 border border-slate-200">Accrual</span></td>
                    <td class="text-right font-black text-slate-900">$185,400.00</td>
                    <td class="text-center"><span class="text-yellow-500 font-bold text-xs bg-yellow-50 px-2 py-1 rounded"><i class="fas fa-lock mr-1"></i> Accrued</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>