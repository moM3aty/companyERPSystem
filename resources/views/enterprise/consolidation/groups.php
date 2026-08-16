<?php
// Path: resources/views/enterprise/consolidation/groups.php
$pageTitle = 'Consolidation Groups';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Consolidation Groups</h1>
        <p class="text-sm text-gray-500 mt-1">Define holding structures and subsidiary ownership percentages.</p>
    </div>
    <button class="btn-primary shadow-md">
        <i class="fas fa-plus mr-2"></i> New Group Structure
    </button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Group 1 -->
    <div class="card p-0 overflow-hidden border-t-4 border-t-nour-primary">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-black text-gray-900 text-lg">Nour Trust Holdings (Group)</h3>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Base Currency: USD</p>
            </div>
            <span class="px-2 py-1 bg-green-100 text-green-800 text-[10px] uppercase font-bold rounded">Active</span>
        </div>
        <div class="p-5">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 border-b border-gray-100 pb-1">Subsidiaries</h4>
            <ul class="space-y-3">
                <li class="flex justify-between items-center text-sm">
                    <div class="flex items-center"><i class="fas fa-building text-nour-primary w-5 text-center mr-2"></i> <span class="font-bold text-gray-700">Nour Enterprise KSA</span></div>
                    <span class="font-mono text-gray-600 bg-gray-100 px-2 py-0.5 rounded text-xs">100% Ownership</span>
                </li>
                <li class="flex justify-between items-center text-sm">
                    <div class="flex items-center"><i class="fas fa-building text-nour-primary w-5 text-center mr-2"></i> <span class="font-bold text-gray-700">Nour Logistics UAE</span></div>
                    <span class="font-mono text-gray-600 bg-gray-100 px-2 py-0.5 rounded text-xs">80% Ownership</span>
                </li>
                <li class="flex justify-between items-center text-sm opacity-80">
                    <div class="flex items-center"><i class="far fa-building text-gray-400 w-5 text-center mr-2"></i> <span class="font-medium text-gray-600">Alpha Manufacturing</span></div>
                    <span class="font-mono text-gray-500 bg-gray-100 px-2 py-0.5 rounded text-xs">51% Ownership (Minority Interest)</span>
                </li>
            </ul>
        </div>
        <div class="p-3 bg-gray-50 border-t border-gray-100 text-right">
            <button class="text-sm font-medium text-nour-primary hover:underline">Edit Structure</button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>