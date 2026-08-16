<?php
// Path: resources/views/crm/partials/filters.php
/* Reusable Filter Bar for Customers Index */
?>
<div class="card p-4 mb-6 bg-white">
    <form action="<?= $actionUrl ?? '/crm/customers' ?>" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
        <div class="flex-1 w-full">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" placeholder="Search by name, email, or phone..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50">
            </div>
        </div>
        <div class="w-full md:w-48">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</label>
            <select name="status" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="lead">Lead</option>
            </select>
        </div>
        <div class="w-full md:w-auto">
            <button type="submit" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md border border-gray-300 text-sm font-medium transition-colors">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
        </div>
    </form>
</div>