<?php
// Path: resources/views/projects/create.php
$pageTitle = 'Create Project';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/projects" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Projects</a>
        <h1 class="text-2xl font-bold text-gray-800">Initiate New Project</h1>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-3">
        <a href="/projects" class="bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">Cancel</a>
        <button type="submit" form="projectForm" class="btn-primary px-8 shadow-md">Save Project</button>
    </div>
</div>

<form id="projectForm" action="/api/v1/projects/create" method="POST">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">General Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Project Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="e.g., ERP Phase 2 Implementation" class="w-full px-4 py-2.5 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Customer / Client</label>
                        <select name="customer_id" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="">Internal Project</option>
                            <option value="1">Alpha Tech Solutions</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Project Manager <span class="text-red-500">*</span></label>
                        <select name="manager_id" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="1">Admin User</option>
                            <option value="2">Ahmed Hassan</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="card p-6 bg-gray-50 border-t-4 border-t-nour-primary">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">Financials & Timeline</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Allocated Budget</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500">$</span></div>
                            <input type="number" step="0.01" name="budget" value="0.00" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm text-right">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cost Center (GL)</label>
                        <select name="cost_center_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="">None</option>
                            <option value="1">CC-101 (IT Projects)</option>
                        </select>
                    </div>
                    <hr class="border-gray-200">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Estimated End Date</label>
                        <input type="date" name="end_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>