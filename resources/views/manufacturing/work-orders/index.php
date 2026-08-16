<?php
// Path: resources/views/manufacturing/work-orders/index.php
$pageTitle = 'Work Orders Dashboard';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span>Manufacturing</span></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Work Orders</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Production Work Orders (MO)</h1>
        <p class="text-sm text-gray-500 mt-1">Track manufacturing execution, material consumption, and time tracking.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-md text-sm font-medium hover:bg-gray-50 shadow-sm">
            <i class="fas fa-calendar-alt mr-1 text-gray-400"></i> Planning Board
        </button>
        <button class="btn-primary shadow-md flex items-center">
            <i class="fas fa-plus mr-2"></i> New Order
        </button>
    </div>
</div>

<!-- MO Status Kanban/List Hybrid -->
<div class="card p-0 overflow-visible">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex flex-wrap justify-between items-center gap-4">
        <div class="flex gap-2 w-full md:w-auto">
            <input type="text" placeholder="Search MO# or Product..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-full md:w-64">
        </div>
        <div class="flex text-xs font-bold text-gray-500 gap-4 uppercase tracking-wider">
            <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-yellow-400 mr-2"></span> Draft/Planned</span>
            <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span> In Progress</span>
            <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span> Done</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Order No.</th>
                    <th class="p-4 font-semibold">Finished Product</th>
                    <th class="p-4 font-semibold text-center">To Produce</th>
                    <th class="p-4 font-semibold">Scheduled Date</th>
                    <th class="p-4 font-semibold text-center">Material Availability</th>
                    <th class="p-4 font-semibold w-48">Production Progress</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <!-- Row: In Progress -->
                <tr class="hover:bg-blue-50/20 transition-colors group">
                    <td class="p-4 font-bold text-nour-primary">MO-2608-004</td>
                    <td class="p-4">
                        <span class="font-bold text-gray-800">Custom Server Rack Unit - 42U</span>
                        <p class="text-[10px] text-gray-500 mt-0.5">BOM: BOM-001</p>
                    </td>
                    <td class="p-4 text-center font-bold text-gray-900 text-base">5 <span class="text-xs text-gray-500 font-normal">Units</span></td>
                    <td class="p-4 text-gray-600">Today, 08:00 AM</td>
                    <td class="p-4 text-center"><span class="text-green-600 font-bold text-[10px] uppercase bg-green-50 px-2 py-0.5 rounded border border-green-200">Available</span></td>
                    <td class="p-4">
                        <div class="flex justify-between text-[10px] font-bold text-blue-700 mb-1 uppercase">
                            <span>Op2: Wiring</span>
                            <span>60%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 60%"></div>
                        </div>
                    </td>
                    <td class="p-4 text-right">
                        <button class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1 rounded text-xs font-bold transition-colors">Tablet View</button>
                    </td>
                </tr>

                <!-- Row: Planned -->
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 font-bold text-gray-600">MO-2608-005</td>
                    <td class="p-4">
                        <span class="font-bold text-gray-800">Standard Desk Frame V2</span>
                        <p class="text-[10px] text-gray-500 mt-0.5">BOM: BOM-014</p>
                    </td>
                    <td class="p-4 text-center font-bold text-gray-900 text-base">20 <span class="text-xs text-gray-500 font-normal">Units</span></td>
                    <td class="p-4 text-gray-600">Aug 18, 2026</td>
                    <td class="p-4 text-center"><span class="text-red-600 font-bold text-[10px] uppercase bg-red-50 px-2 py-0.5 rounded border border-red-200">Waiting Components</span></td>
                    <td class="p-4">
                        <span class="text-xs font-bold text-yellow-600 uppercase">Planned</span>
                    </td>
                    <td class="p-4 text-right">
                        <button class="bg-gray-100 text-gray-600 hover:bg-gray-200 px-3 py-1 rounded text-xs font-bold transition-colors">Start</button>
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