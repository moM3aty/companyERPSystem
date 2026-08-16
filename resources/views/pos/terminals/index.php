<?php
// Path: resources/views/pos/terminals/index.php
$pageTitle = 'POS Terminals';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">POS Terminals (Registers)</h1>
        <p class="text-sm text-gray-500 mt-1">Manage physical points of sale across all branches.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md flex items-center">
            <i class="fas fa-plus mr-2"></i> Register Terminal
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Active Terminal Card -->
    <div class="card p-0 overflow-hidden border-t-4 border-t-green-500">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-start">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Main Register (Front)</h3>
                <p class="text-xs text-gray-500 font-mono mt-0.5">TERM-01</p>
            </div>
            <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Online</span>
        </div>
        <div class="p-5 space-y-3">
            <div class="flex items-center text-sm text-gray-600">
                <i class="fas fa-building w-5 text-gray-400"></i> HQ - Riyadh Branch
            </div>
            <div class="flex items-center text-sm text-gray-600">
                <i class="fas fa-user-tag w-5 text-nour-primary"></i> Active Shift: Ahmed Hassan
            </div>
            <div class="flex items-center text-sm text-gray-600">
                <i class="fas fa-sync w-5 text-gray-400"></i> Last Sync: Just now
            </div>
        </div>
        <div class="p-3 border-t border-gray-100 bg-gray-50 text-right">
            <button class="text-sm font-medium text-gray-500 hover:text-nour-primary">Settings</button>
        </div>
    </div>

    <!-- Inactive Terminal Card -->
    <div class="card p-0 overflow-hidden border-t-4 border-t-gray-300 opacity-80">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-start">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Electronics Dept Reg</h3>
                <p class="text-xs text-gray-500 font-mono mt-0.5">TERM-02</p>
            </div>
            <span class="bg-gray-200 text-gray-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Offline</span>
        </div>
        <div class="p-5 space-y-3">
            <div class="flex items-center text-sm text-gray-600">
                <i class="fas fa-building w-5 text-gray-400"></i> HQ - Riyadh Branch
            </div>
            <div class="flex items-center text-sm text-gray-600">
                <i class="fas fa-user-tag w-5 text-gray-300"></i> No Active Shift
            </div>
            <div class="flex items-center text-sm text-gray-600">
                <i class="fas fa-sync w-5 text-gray-400"></i> Last Sync: Yesterday
            </div>
        </div>
        <div class="p-3 border-t border-gray-100 bg-gray-50 text-right">
            <button class="text-sm font-medium text-gray-500 hover:text-nour-primary">Settings</button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>