<?php
// Path: resources/views/enterprise/advanced-hr/training.php
$pageTitle = 'Training & Development';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Training Programs</h1>
        <p class="text-sm text-gray-500 mt-1">Manage employee skill development, scheduled courses, and budgets.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md flex items-center">
            <i class="fas fa-plus mr-2"></i> Schedule Program
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Active Program Card -->
    <div class="card p-0 overflow-hidden border-t-4 border-t-nour-primary col-span-1">
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-bold text-gray-900 text-lg leading-tight">Advanced ZATCA Integration</h3>
                <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Active</span>
            </div>
            <p class="text-xs text-gray-500"><i class="fas fa-chalkboard-teacher mr-1"></i> Inst: Dr. Ahmed Yassin</p>
        </div>
        <div class="p-5">
            <div class="flex justify-between items-center mb-4 text-sm">
                <span class="text-gray-500">Duration:</span>
                <span class="font-semibold text-gray-800">15 Aug - 20 Aug 2026</span>
            </div>
            <div class="mb-2">
                <div class="flex justify-between items-center mb-1 text-xs">
                    <span class="font-medium text-gray-700">Enrolled (14/20)</span>
                    <span class="font-bold text-nour-primary">70% Full</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-nour-primary h-1.5 rounded-full" style="width: 70%"></div>
                </div>
            </div>
        </div>
        <div class="p-3 border-t border-gray-100 flex justify-between items-center bg-gray-50">
            <span class="text-xs font-bold text-gray-500">Budget: $2,500</span>
            <button class="text-nour-primary hover:underline text-sm font-medium">Manage Attendees</button>
        </div>
    </div>

    <!-- Planned Program Card -->
    <div class="card p-0 overflow-hidden border-t-4 border-t-yellow-500 col-span-1 opacity-90">
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-bold text-gray-900 text-lg leading-tight">Leadership Essentials Q4</h3>
                <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Planned</span>
            </div>
            <p class="text-xs text-gray-500"><i class="fas fa-chalkboard-teacher mr-1"></i> Inst: External Vendor</p>
        </div>
        <div class="p-5">
            <div class="flex justify-between items-center mb-4 text-sm">
                <span class="text-gray-500">Starts In:</span>
                <span class="font-semibold text-gray-800">45 Days</span>
            </div>
            <div class="mb-2">
                <div class="flex justify-between items-center mb-1 text-xs">
                    <span class="font-medium text-gray-700">Enrolled (5/30)</span>
                    <span class="font-bold text-yellow-600">Open</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-yellow-500 h-1.5 rounded-full" style="width: 15%"></div>
                </div>
            </div>
        </div>
        <div class="p-3 border-t border-gray-100 flex justify-between items-center bg-gray-50">
            <span class="text-xs font-bold text-gray-500">Budget: $10,000</span>
            <button class="text-nour-primary hover:underline text-sm font-medium">Manage Attendees</button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>