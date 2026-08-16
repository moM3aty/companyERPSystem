<?php
// Path: resources/views/profile/notifications.php
$pageTitle = 'Notification Preferences';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Notification Settings</h1>
        <p class="text-sm text-gray-500 mt-1">Control how and when you receive alerts from the ERP system.</p>
    </div>
</div>

<div class="border-b border-gray-200 mb-6">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <a href="/profile" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
            <i class="far fa-user mr-2"></i> Overview
        </a>
        <a href="/profile/edit" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
            <i class="far fa-edit mr-2"></i> Edit Profile
        </a>
        <a href="/profile/security" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
            <i class="fas fa-shield-alt mr-2"></i> Security & 2FA
        </a>
        <a href="/profile/notifications" class="border-nour-primary text-nour-primary whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            <i class="fas fa-bell mr-2"></i> Notifications
        </a>
        <a href="/profile/activity" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
            <i class="fas fa-history mr-2"></i> Activity Log
        </a>
    </nav>
</div>

<div class="card p-0 max-w-4xl">
    <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Delivery Channels</h3>
    </div>
    
    <div class="p-6 space-y-8">
        
        <!-- Category: Approvals -->
        <div>
            <h4 class="text-sm font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Approvals & Workflow</h4>
            <div class="space-y-4">
                <div class="flex items-center justify-between hover:bg-gray-50 p-2 rounded transition-colors">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Purchase Orders</p>
                        <p class="text-xs text-gray-500">When a PO requires your approval</p>
                    </div>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center text-sm text-gray-600 cursor-pointer"><input type="checkbox" checked class="rounded text-nour-primary mr-2 focus:ring-nour-primary"> In-App</label>
                        <label class="flex items-center text-sm text-gray-600 cursor-pointer"><input type="checkbox" checked class="rounded text-nour-primary mr-2 focus:ring-nour-primary"> Email</label>
                    </div>
                </div>
                <div class="flex items-center justify-between hover:bg-gray-50 p-2 rounded transition-colors">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Leave Requests</p>
                        <p class="text-xs text-gray-500">When an employee requests time off</p>
                    </div>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center text-sm text-gray-600 cursor-pointer"><input type="checkbox" checked class="rounded text-nour-primary mr-2 focus:ring-nour-primary"> In-App</label>
                        <label class="flex items-center text-sm text-gray-600 cursor-pointer"><input type="checkbox" class="rounded text-nour-primary mr-2 focus:ring-nour-primary"> Email</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category: System Alerts -->
        <div>
            <h4 class="text-sm font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">System & Operations Alerts</h4>
            <div class="space-y-4">
                <div class="flex items-center justify-between hover:bg-gray-50 p-2 rounded transition-colors">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Low Stock Warnings</p>
                        <p class="text-xs text-gray-500">When inventory drops below reorder point</p>
                    </div>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center text-sm text-gray-600 cursor-pointer"><input type="checkbox" checked class="rounded text-nour-primary mr-2 focus:ring-nour-primary"> In-App</label>
                        <label class="flex items-center text-sm text-gray-600 cursor-pointer"><input type="checkbox" checked class="rounded text-nour-primary mr-2 focus:ring-nour-primary"> Email</label>
                    </div>
                </div>
                <div class="flex items-center justify-between hover:bg-gray-50 p-2 rounded transition-colors">
                    <div>
                        <p class="text-sm font-medium text-gray-800">ZATCA Integration Errors</p>
                        <p class="text-xs text-red-500 font-medium">Critical failures in E-Invoicing sync</p>
                    </div>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center text-sm text-gray-600 opacity-70 cursor-not-allowed"><input type="checkbox" checked disabled class="rounded text-nour-primary mr-2"> In-App</label>
                        <label class="flex items-center text-sm text-gray-600 opacity-70 cursor-not-allowed"><input type="checkbox" checked disabled class="rounded text-nour-primary mr-2"> Email</label>
                        <span class="text-[10px] bg-gray-200 text-gray-600 px-2 py-0.5 rounded font-bold uppercase">Mandatory</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="p-4 border-t border-gray-100 bg-gray-50 text-right">
        <button class="btn-primary shadow-md">Save Preferences</button>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>