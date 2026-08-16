<?php
// Path: resources/views/enterprise/supply-chain/planning.php
$pageTitle = 'Master Production Schedule';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Master Production Schedule (MPS)</h1>
        <p class="text-sm text-gray-500 mt-1">Visual timeline for manufacturing orders and expected supply receipts.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50">
            <i class="fas fa-filter"></i>
        </button>
        <div class="inline-flex rounded-md shadow-sm" role="group">
            <button type="button" class="px-4 py-1.5 text-sm font-medium text-nour-primary bg-white border border-gray-300 rounded-l-lg hover:bg-gray-50 focus:z-10 focus:ring-2 focus:ring-nour-primary">Week</button>
            <button type="button" class="px-4 py-1.5 text-sm font-medium text-gray-900 bg-gray-100 border-t border-b border-gray-300 hover:bg-gray-200 focus:z-10 focus:ring-2 focus:ring-nour-primary">Month</button>
            <button type="button" class="px-4 py-1.5 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-50 focus:z-10 focus:ring-2 focus:ring-nour-primary">Quarter</button>
        </div>
    </div>
</div>

<div class="card p-0 overflow-hidden min-h-[600px] flex flex-col">
    <div class="p-3 bg-gray-800 text-white flex justify-between items-center text-sm font-medium">
        <div class="flex items-center gap-4">
            <button class="hover:text-gray-300"><i class="fas fa-chevron-left"></i></button>
            <span>August 2026</span>
            <button class="hover:text-gray-300"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div class="flex gap-4 text-xs">
            <span class="flex items-center"><span class="w-3 h-3 rounded bg-blue-500 mr-1"></span> Production</span>
            <span class="flex items-center"><span class="w-3 h-3 rounded bg-green-500 mr-1"></span> Inbound Supply</span>
        </div>
    </div>
    
    <!-- Mockup of a Gantt Board -->
    <div class="flex-1 flex overflow-x-auto bg-gray-50 custom-scrollbar relative">
        <!-- Resource Column -->
        <div class="w-48 flex-shrink-0 border-r border-gray-300 bg-white z-20 sticky left-0 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
            <div class="h-10 border-b border-gray-200 bg-gray-100 font-bold text-xs text-gray-500 uppercase flex items-center px-3">Resource / Line</div>
            <div class="h-16 border-b border-gray-100 flex items-center px-3 text-sm font-bold text-gray-800">Assmbly Line 1</div>
            <div class="h-16 border-b border-gray-100 flex items-center px-3 text-sm font-bold text-gray-800">Assmbly Line 2</div>
            <div class="h-16 border-b border-gray-100 flex items-center px-3 text-sm font-bold text-gray-800">Packaging Sta.</div>
            <div class="h-16 border-b border-gray-100 flex items-center px-3 text-sm font-bold text-gray-800 bg-green-50/30">HQ Warehouse</div>
        </div>

        <!-- Timeline Grid -->
        <div class="flex-1 min-w-[800px] relative">
            <!-- Headers (Days) -->
            <div class="h-10 border-b border-gray-200 flex bg-gray-50">
                <div class="flex-1 border-r border-gray-200 flex flex-col justify-center items-center text-[10px] text-gray-500"><span class="font-bold">Mon</span><span>10</span></div>
                <div class="flex-1 border-r border-gray-200 flex flex-col justify-center items-center text-[10px] text-gray-500"><span class="font-bold">Tue</span><span>11</span></div>
                <div class="flex-1 border-r border-gray-200 flex flex-col justify-center items-center text-[10px] text-gray-500"><span class="font-bold text-nour-primary">Wed</span><span class="text-nour-primary">12</span></div>
                <div class="flex-1 border-r border-gray-200 flex flex-col justify-center items-center text-[10px] text-gray-500"><span class="font-bold">Thu</span><span>13</span></div>
                <div class="flex-1 border-r border-gray-200 flex flex-col justify-center items-center text-[10px] text-gray-500"><span class="font-bold">Fri</span><span>14</span></div>
                <div class="flex-1 border-r border-gray-200 flex flex-col justify-center items-center text-[10px] text-gray-400 bg-gray-100"><span class="font-bold">Sat</span><span>15</span></div>
                <div class="flex-1 border-r border-gray-200 flex flex-col justify-center items-center text-[10px] text-gray-400 bg-gray-100"><span class="font-bold">Sun</span><span>16</span></div>
            </div>

            <!-- Row 1 -->
            <div class="h-16 border-b border-gray-100 flex relative group hover:bg-gray-100/50 transition-colors">
                <div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed bg-gray-100/30"></div><div class="flex-1 border-r border-gray-100 border-dashed bg-gray-100/30"></div>
                <!-- Task Bar -->
                <div class="absolute top-3 left-[15%] w-[40%] h-10 bg-blue-500 rounded-md shadow-sm border border-blue-600 flex items-center px-3 text-white text-xs font-medium cursor-pointer hover:bg-blue-600 transition-colors z-10 truncate">
                    PROD-2608-01 (Server Racks)
                </div>
            </div>
            
            <!-- Row 2 -->
            <div class="h-16 border-b border-gray-100 flex relative group hover:bg-gray-100/50 transition-colors">
                <div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed bg-gray-100/30"></div><div class="flex-1 border-r border-gray-100 border-dashed bg-gray-100/30"></div>
                <!-- Task Bar -->
                <div class="absolute top-3 left-[50%] w-[30%] h-10 bg-blue-400 rounded-md shadow-sm border border-blue-500 flex items-center px-3 text-white text-xs font-medium cursor-pointer hover:bg-blue-500 transition-colors z-10 truncate opacity-80">
                    PROD-2608-02 (Desks)
                </div>
            </div>

             <!-- Row 3 -->
             <div class="h-16 border-b border-gray-100 flex relative group hover:bg-gray-100/50 transition-colors">
                <div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed bg-gray-100/30"></div><div class="flex-1 border-r border-gray-100 border-dashed bg-gray-100/30"></div>
            </div>

            <!-- Row 4 (Warehouse Inbound) -->
            <div class="h-16 border-b border-gray-100 flex relative group bg-green-50/10 hover:bg-green-50/30 transition-colors">
                <div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed"></div><div class="flex-1 border-r border-gray-100 border-dashed bg-gray-100/30"></div><div class="flex-1 border-r border-gray-100 border-dashed bg-gray-100/30"></div>
                <!-- Milestone/Delivery Marker -->
                <div class="absolute top-4 left-[80%] transform -translate-x-1/2 flex flex-col items-center z-10 cursor-pointer">
                    <div class="w-4 h-4 rotate-45 bg-green-500 border border-green-700 shadow-sm mb-1"></div>
                    <span class="text-[10px] font-bold text-green-700 bg-white px-1 rounded shadow-sm border border-green-100">PO-105 Arrives</span>
                </div>
            </div>
            
            <!-- Current Day Line -->
            <div class="absolute top-0 bottom-0 left-[35%] w-px bg-red-400 z-0"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>