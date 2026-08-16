<?php
// Path: resources/views/maintenance/work-orders/create.php
$pageTitle = 'Create Work Order';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/maintenance/work-orders" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Work Orders</a>
        <h1 class="text-2xl font-bold text-gray-800">New Maintenance Work Order</h1>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-3">
        <button type="button" onclick="window.history.back()" class="bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">Cancel</button>
        <button type="submit" form="woForm" class="btn-primary px-8 shadow-md">Create WO</button>
    </div>
</div>

<form id="woForm" action="/api/v1/maintenance/work-orders" method="POST">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6 border-t-4 border-t-nour-primary">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Issue Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Target Asset / Machine <span class="text-red-500">*</span></label>
                        <select name="asset_id" required class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm bg-gray-50">
                            <option value="">Select an asset...</option>
                            <option value="1">AST-VEH-0012 (Delivery Truck - Isuzu)</option>
                            <option value="2">AST-MAC-044 (CNC Machine Alpha)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Maintenance Type <span class="text-red-500">*</span></label>
                        <select name="type" required class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="corrective">Corrective (Breakdown/Repair)</option>
                            <option value="preventive">Preventive (Routine)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Priority</label>
                        <select name="priority" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="low">Low</option>
                            <option value="normal" selected>Normal</option>
                            <option value="high">High</option>
                            <option value="critical">Critical (Machine Down)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Issue Description / Instructions <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="4" required class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm" placeholder="Describe the problem or list the maintenance steps required..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Spare Parts Request -->
            <div class="card p-0">
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-base font-bold text-gray-800">Required Spare Parts (Optional)</h3>
                    <button type="button" class="text-xs font-bold text-nour-primary hover:underline"><i class="fas fa-plus mr-1"></i> Add Part</button>
                </div>
                <div class="p-4 text-center text-gray-400 py-8">
                    <p class="text-sm">No spare parts added to this work order yet.</p>
                    <p class="text-xs mt-1">If parts are added, they will be reserved from inventory upon WO approval.</p>
                </div>
            </div>
        </div>

        <!-- Right Column (Scheduling & Assignment) -->
        <div class="lg:col-span-1 space-y-6">
            <div class="card p-6 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">Scheduling</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Scheduled Date <span class="text-red-500">*</span></label>
                        <input type="date" name="scheduled_date" value="<?= date('Y-m-d') ?>" required class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Estimated Duration (Hrs)</label>
                        <input type="number" step="0.5" name="estimated_hours" placeholder="e.g. 2.5" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Assignment</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Assign to Technician</label>
                        <select name="assigned_to" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="">Unassigned</option>
                            <option value="15">Ali Omar (Internal Tech)</option>
                            <option value="vendor_1">External Vendor (SafeLift)</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                        <input type="checkbox" id="notify" name="notify_tech" checked class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary w-4 h-4">
                        <label for="notify" class="text-sm text-gray-700">Send notification to assignee</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>