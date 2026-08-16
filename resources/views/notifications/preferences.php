<?php
// Path: resources/views/notifications/preferences.php
$pageTitle = 'Notification Preferences';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/notifications" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Inbox</a>
        <h1 class="text-2xl font-bold text-gray-800">Notification Settings</h1>
        <p class="text-sm text-gray-500 mt-1">Control how and when you receive alerts from the ERP system.</p>
    </div>
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
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Purchase Orders</p>
                        <p class="text-xs text-gray-500">When a PO requires your approval</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center text-sm text-gray-600"><input type="checkbox" checked class="rounded text-nour-primary mr-2"> In-App</label>
                        <label class="flex items-center text-sm text-gray-600"><input type="checkbox" checked class="rounded text-nour-primary mr-2"> Email</label>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Leave Requests</p>
                        <p class="text-xs text-gray-500">When an employee requests time off</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center text-sm text-gray-600"><input type="checkbox" checked class="rounded text-nour-primary mr-2"> In-App</label>
                        <label class="flex items-center text-sm text-gray-600"><input type="checkbox" class="rounded text-nour-primary mr-2"> Email</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category: System Alerts -->
        <div>
            <h4 class="text-sm font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">System & Operations Alerts</h4>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Low Stock Warnings</p>
                        <p class="text-xs text-gray-500">When inventory drops below reorder point</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center text-sm text-gray-600"><input type="checkbox" checked class="rounded text-nour-primary mr-2"> In-App</label>
                        <label class="flex items-center text-sm text-gray-600"><input type="checkbox" checked class="rounded text-nour-primary mr-2"> Email</label>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800">ZATCA Integration Errors</p>
                        <p class="text-xs text-red-500">Critical failures in E-Invoicing sync</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center text-sm text-gray-600"><input type="checkbox" checked disabled class="rounded text-nour-primary mr-2"> In-App</label>
                        <label class="flex items-center text-sm text-gray-600"><input type="checkbox" checked disabled class="rounded text-nour-primary mr-2"> Email</label>
                        <span class="text-[10px] text-gray-400 italic">Mandatory</span>
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