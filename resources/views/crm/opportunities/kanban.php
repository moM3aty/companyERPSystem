<?php
// Path: resources/views/crm/opportunities/kanban.php
$pageTitle = 'Sales Pipeline';
ob_start();
?>

<div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Sales Pipeline</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Opportunities Board</h1>
        <p class="text-sm text-gray-500 mt-1">Drag and drop deals through the sales stages to forecast revenue.</p>
    </div>
    
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-3 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-filter mr-1"></i> My Deals
        </button>
        <button class="btn-primary shadow-md flex items-center">
            <i class="fas fa-plus mr-2"></i> New Deal
        </button>
    </div>
</div>

<!-- Pipeline Kanban -->
<div class="flex gap-4 overflow-x-auto pb-4 h-[calc(100vh-180px)] custom-scrollbar items-start mt-4">
    
    <!-- Stage: Prospecting -->
    <div class="flex flex-col bg-gray-100/60 rounded-lg w-[300px] min-w-[300px] flex-shrink-0 border border-gray-200">
        <div class="p-3 border-b border-gray-200">
            <div class="flex justify-between items-center mb-1">
                <h3 class="font-bold text-gray-700 uppercase tracking-wider text-xs">1. Prospecting</h3>
                <span class="bg-gray-200 text-gray-700 text-xs px-2 py-0.5 rounded font-bold">2 Deals</span>
            </div>
            <p class="text-lg font-black text-gray-900">$35,000</p>
        </div>
        <div class="p-2 overflow-y-auto custom-scrollbar flex flex-col gap-2 min-h-[150px]">
            <!-- Deal Card -->
            <div class="bg-white p-3 rounded shadow-sm border-l-4 border-l-gray-400 cursor-grab hover:shadow-md transition-shadow">
                <h4 class="font-bold text-gray-800 text-sm mb-1">ERP Cloud Migration</h4>
                <p class="text-xs text-gray-500 mb-3"><i class="fas fa-building mr-1"></i> Future Builders LLC</p>
                <div class="flex justify-between items-end">
                    <span class="font-black text-nour-primary">$25,000</span>
                    <span class="text-[10px] font-bold bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">10% Prob.</span>
                </div>
            </div>
             <!-- Deal Card -->
             <div class="bg-white p-3 rounded shadow-sm border-l-4 border-l-gray-400 cursor-grab hover:shadow-md transition-shadow">
                <h4 class="font-bold text-gray-800 text-sm mb-1">Annual Support Contract</h4>
                <p class="text-xs text-gray-500 mb-3"><i class="fas fa-building mr-1"></i> Skyline Retail</p>
                <div class="flex justify-between items-end">
                    <span class="font-black text-nour-primary">$10,000</span>
                    <span class="text-[10px] font-bold bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">20% Prob.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stage: Proposal Sent -->
    <div class="flex flex-col bg-blue-50/40 rounded-lg w-[300px] min-w-[300px] flex-shrink-0 border border-blue-100">
        <div class="p-3 border-b border-blue-200">
            <div class="flex justify-between items-center mb-1">
                <h3 class="font-bold text-blue-800 uppercase tracking-wider text-xs">2. Proposal Sent</h3>
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded font-bold">1 Deal</span>
            </div>
            <p class="text-lg font-black text-gray-900">$120,000</p>
        </div>
        <div class="p-2 overflow-y-auto custom-scrollbar flex flex-col gap-2 min-h-[150px]">
            <!-- Deal Card -->
            <div class="bg-white p-3 rounded shadow-sm border-l-4 border-l-blue-400 cursor-grab hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-1">
                    <h4 class="font-bold text-gray-800 text-sm">Server Infrastructure</h4>
                    <i class="fas fa-fire text-orange-500" title="Hot Deal"></i>
                </div>
                <p class="text-xs text-gray-500 mb-3"><i class="fas fa-building mr-1"></i> Global Pharma Group</p>
                <div class="flex justify-between items-end">
                    <span class="font-black text-nour-primary">$120,000</span>
                    <span class="text-[10px] font-bold bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded">50% Prob.</span>
                </div>
                <div class="mt-2 pt-2 border-t border-gray-100 text-xs text-gray-400 flex items-center">
                    <i class="far fa-clock mr-1"></i> Follow up today
                </div>
            </div>
        </div>
    </div>

    <!-- Stage: Negotiation -->
    <div class="flex flex-col bg-yellow-50/40 rounded-lg w-[300px] min-w-[300px] flex-shrink-0 border border-yellow-100">
        <div class="p-3 border-b border-yellow-200">
            <div class="flex justify-between items-center mb-1">
                <h3 class="font-bold text-yellow-800 uppercase tracking-wider text-xs">3. Negotiation</h3>
                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded font-bold">0 Deals</span>
            </div>
            <p class="text-lg font-black text-gray-900">$0</p>
        </div>
        <div class="p-2 overflow-y-auto custom-scrollbar flex flex-col gap-2 items-center justify-center min-h-[150px] border-2 border-dashed border-gray-200 m-2 rounded">
            <p class="text-xs text-gray-400 font-medium">Drag deals here</p>
        </div>
    </div>

    <!-- Stage: Won -->
    <div class="flex flex-col bg-green-50/40 rounded-lg w-[300px] min-w-[300px] flex-shrink-0 border border-green-200">
        <div class="p-3 border-b border-green-200">
            <div class="flex justify-between items-center mb-1">
                <h3 class="font-bold text-green-800 uppercase tracking-wider text-xs">4. Won (Closed)</h3>
                <span class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded font-bold">1 Deal</span>
            </div>
            <p class="text-lg font-black text-green-700">$45,000</p>
        </div>
        <div class="p-2 overflow-y-auto custom-scrollbar flex flex-col gap-2 min-h-[150px]">
            <!-- Deal Card -->
            <div class="bg-white p-3 rounded shadow-sm border-l-4 border-l-green-500 opacity-80">
                <div class="flex justify-between items-start mb-1">
                    <h4 class="font-bold text-gray-800 text-sm">ERP Phase 2</h4>
                    <i class="fas fa-trophy text-yellow-500"></i>
                </div>
                <p class="text-xs text-gray-500 mb-3"><i class="fas fa-building mr-1"></i> Alpha Tech Solutions</p>
                <div class="flex justify-between items-end">
                    <span class="font-black text-green-600">$45,000</span>
                    <span class="text-[10px] font-bold bg-green-100 text-green-800 px-1.5 py-0.5 rounded">100% Prob.</span>
                </div>
                <button class="w-full mt-3 bg-gray-50 border border-gray-200 py-1 text-xs font-bold text-gray-600 rounded hover:bg-gray-100">Create Sales Order</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>