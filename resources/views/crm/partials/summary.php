<?php
// Path: resources/views/crm/partials/summary.php
/* Reusable KPI Summary for CRM / Customers */
?>
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="card p-5 border-l-4 border-l-nour-primary">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Customers</p>
        <h3 class="text-2xl font-bold text-gray-900"><?= $totalCustomers ?? '1,245' ?></h3>
    </div>
    <div class="card p-5 border-l-4 border-l-green-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Active Accounts</p>
        <h3 class="text-2xl font-bold text-gray-900"><?= $activeCustomers ?? '850' ?></h3>
    </div>
    <div class="card p-5 border-l-4 border-l-yellow-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Leads & Prospects</p>
        <h3 class="text-2xl font-bold text-gray-900"><?= $totalLeads ?? '395' ?></h3>
    </div>
    <div class="card p-5 border-l-4 border-l-blue-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">New This Month</p>
        <h3 class="text-2xl font-bold text-gray-900"><?= $newThisMonth ?? '42' ?></h3>
    </div>
</div>