<?php
// Path: resources/views/projects/kanban.php
$pageTitle = 'Projects Kanban Board';
ob_start();
?>

<!-- Header -->
<div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">ERP Implementation Phase 2</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Task Board</h1>
    </div>
    
    <div class="mt-4 sm:mt-0 flex gap-2 items-center">
        <!-- Team Avatars -->
        <div class="flex -space-x-2 mr-4">
            <img class="w-8 h-8 rounded-full border-2 border-white shadow-sm" src="https://ui-avatars.com/api/?name=Ahmed&background=005eb8&color=fff" alt="User">
            <img class="w-8 h-8 rounded-full border-2 border-white shadow-sm" src="https://ui-avatars.com/api/?name=Sarah&background=f59e0b&color=fff" alt="User">
            <div class="w-8 h-8 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500 shadow-sm">+3</div>
        </div>
        <button class="bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-filter mr-1"></i> Filter
        </button>
        <button class="btn-primary py-1.5 px-3 text-sm flex items-center shadow-md">
            <i class="fas fa-plus mr-1"></i> Add Task
        </button>
    </div>
</div>

<!-- Kanban Board Container -->
<div class="flex gap-6 overflow-x-auto pb-4 h-[calc(100vh-180px)] custom-scrollbar items-start">
    
    <!-- Column: To Do -->
    <div class="flex flex-col bg-gray-100/80 rounded-lg w-80 min-w-[320px] flex-shrink-0 max-h-full">
        <div class="p-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-bold text-gray-700 flex items-center"><span class="w-2 h-2 rounded-full bg-gray-400 mr-2"></span> To Do</h3>
            <span class="bg-gray-200 text-gray-600 text-xs px-2 py-0.5 rounded-full font-bold">3</span>
        </div>
        <div class="p-3 overflow-y-auto custom-scrollbar flex flex-col gap-3">
            
            <!-- Card -->
            <div class="bg-white p-3 rounded shadow-sm border border-gray-200 cursor-pointer hover:border-nour-primary transition-colors group">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-bold uppercase bg-red-100 text-red-800 px-1.5 py-0.5 rounded">High Priority</span>
                    <button class="text-gray-300 hover:text-gray-500 opacity-0 group-hover:opacity-100 transition-opacity"><i class="fas fa-ellipsis-h"></i></button>
                </div>
                <h4 class="text-sm font-semibold text-gray-800 leading-snug mb-2">Configure ZATCA Phase 2 API Endpoints</h4>
                <div class="flex justify-between items-end mt-3">
                    <span class="text-xs text-gray-500 font-medium"><i class="far fa-clock mr-1"></i> Aug 20</span>
                    <img class="w-6 h-6 rounded-full border border-gray-100" src="https://ui-avatars.com/api/?name=Ahmed&background=005eb8&color=fff" alt="Assignee" title="Ahmed Hassan">
                </div>
            </div>

            <!-- Card -->
            <div class="bg-white p-3 rounded shadow-sm border border-gray-200 cursor-pointer hover:border-nour-primary transition-colors group">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-bold uppercase bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded">Database</span>
                </div>
                <h4 class="text-sm font-semibold text-gray-800 leading-snug mb-2">Migrate HR employee records to new schema</h4>
                <div class="flex justify-between items-end mt-3">
                    <span class="text-xs text-gray-400 font-medium"><i class="fas fa-paperclip mr-1"></i> 2</span>
                    <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] text-gray-500"><i class="fas fa-user-plus"></i></div>
                </div>
            </div>

        </div>
        <div class="p-3 border-t border-gray-200">
            <button class="text-sm text-gray-500 hover:text-nour-primary w-full text-left font-medium"><i class="fas fa-plus mr-1"></i> Add a card...</button>
        </div>
    </div>

    <!-- Column: In Progress -->
    <div class="flex flex-col bg-gray-100/80 rounded-lg w-80 min-w-[320px] flex-shrink-0 max-h-full">
        <div class="p-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-bold text-gray-700 flex items-center"><span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span> In Progress</h3>
            <span class="bg-gray-200 text-gray-600 text-xs px-2 py-0.5 rounded-full font-bold">1</span>
        </div>
        <div class="p-3 overflow-y-auto custom-scrollbar flex flex-col gap-3">
            
            <!-- Card -->
            <div class="bg-white p-3 rounded shadow-sm border border-gray-200 cursor-pointer hover:border-nour-primary transition-colors group">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-bold uppercase bg-yellow-100 text-yellow-800 px-1.5 py-0.5 rounded">UI/UX</span>
                </div>
                <h4 class="text-sm font-semibold text-gray-800 leading-snug mb-2">Design responsive Kanban board for Projects module</h4>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2 mt-1">
                    <div class="bg-blue-500 h-1.5 rounded-full" style="width: 75%"></div>
                </div>
                <div class="flex justify-between items-end mt-3">
                    <span class="text-xs text-gray-500 font-medium"><i class="far fa-comments mr-1"></i> 4</span>
                    <img class="w-6 h-6 rounded-full border border-gray-100" src="https://ui-avatars.com/api/?name=Admin&background=0a1930&color=fff" alt="Assignee">
                </div>
            </div>

        </div>
        <div class="p-3 border-t border-gray-200">
            <button class="text-sm text-gray-500 hover:text-nour-primary w-full text-left font-medium"><i class="fas fa-plus mr-1"></i> Add a card...</button>
        </div>
    </div>

    <!-- Column: Review -->
    <div class="flex flex-col bg-gray-100/80 rounded-lg w-80 min-w-[320px] flex-shrink-0 max-h-full">
        <div class="p-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-bold text-gray-700 flex items-center"><span class="w-2 h-2 rounded-full bg-yellow-400 mr-2"></span> In Review</h3>
            <span class="bg-gray-200 text-gray-600 text-xs px-2 py-0.5 rounded-full font-bold">0</span>
        </div>
        <div class="p-3 overflow-y-auto custom-scrollbar flex flex-col gap-3 h-24 items-center justify-center border-2 border-dashed border-gray-300 mx-3 my-2 rounded">
            <p class="text-xs text-gray-400 font-medium">Drop cards here</p>
        </div>
    </div>

    <!-- Column: Done -->
    <div class="flex flex-col bg-gray-100/80 rounded-lg w-80 min-w-[320px] flex-shrink-0 max-h-full opacity-75">
        <div class="p-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-bold text-gray-700 flex items-center"><span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span> Done</h3>
            <span class="bg-gray-200 text-gray-600 text-xs px-2 py-0.5 rounded-full font-bold">12</span>
        </div>
        <div class="p-3 overflow-y-auto custom-scrollbar flex flex-col gap-3">
             <!-- Card -->
             <div class="bg-gray-50 p-3 rounded shadow-sm border border-gray-200">
                <h4 class="text-sm font-semibold text-gray-500 line-through leading-snug mb-2">Setup base application layout (app.php)</h4>
                <div class="flex justify-end mt-2">
                    <span class="text-[10px] text-green-600 font-bold bg-green-100 px-1.5 py-0.5 rounded"><i class="fas fa-check mr-1"></i>Completed</span>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>