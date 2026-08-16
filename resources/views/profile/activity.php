<?php
// Path: resources/views/profile/activity.php
$pageTitle = 'Activity Log';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Activity Log</h1>
        <p class="text-sm text-gray-500 mt-1">A history of actions you've performed within the ERP system.</p>
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
        <a href="/profile/notifications" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
            <i class="fas fa-bell mr-2"></i> Notifications
        </a>
        <a href="/profile/activity" class="border-nour-primary text-nour-primary whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            <i class="fas fa-history mr-2"></i> Activity Log
        </a>
    </nav>
</div>

<div class="card p-0 overflow-hidden max-w-5xl">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex gap-4">
        <input type="date" class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600 w-48">
        <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600">
            <option>All Modules</option>
            <option>Authentication</option>
            <option>Sales & CRM</option>
            <option>Accounting</option>
        </select>
        <button class="bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-md text-sm font-medium hover:bg-gray-100 shadow-sm">
            Filter
        </button>
    </div>

    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold w-48">Date & Time</th>
                    <th class="p-4 font-semibold w-32">Module</th>
                    <th class="p-4 font-semibold">Action Description</th>
                    <th class="p-4 font-semibold text-center w-24">IP Address</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100 text-gray-700">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 text-gray-500 font-mono text-xs">Aug 15, 2026 14:32:01</td>
                    <td class="p-4"><span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Accounting</span></td>
                    <td class="p-4 font-medium text-gray-900">Approved Journal Entry <a href="#" class="text-nour-primary hover:underline">#JE-2026-08012</a></td>
                    <td class="p-4 text-center text-xs text-gray-400">192.168.1.105</td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 text-gray-500 font-mono text-xs">Aug 15, 2026 10:15:44</td>
                    <td class="p-4"><span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Sales</span></td>
                    <td class="p-4 font-medium text-gray-900">Created Sales Invoice <a href="#" class="text-nour-primary hover:underline">#INV-2026-089</a></td>
                    <td class="p-4 text-center text-xs text-gray-400">192.168.1.105</td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 text-gray-500 font-mono text-xs">Aug 15, 2026 09:02:12</td>
                    <td class="p-4"><span class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Auth</span></td>
                    <td class="p-4 font-medium text-gray-900">Logged into the system successfully.</td>
                    <td class="p-4 text-center text-xs text-gray-400">192.168.1.105</td>
                </tr>
                <tr class="hover:bg-red-50/30 transition-colors">
                    <td class="p-4 text-gray-500 font-mono text-xs">Aug 14, 2026 18:45:00</td>
                    <td class="p-4"><span class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Auth</span></td>
                    <td class="p-4 font-medium text-red-600">Failed login attempt (Incorrect Password).</td>
                    <td class="p-4 text-center text-xs text-red-400 font-bold">84.23.11.90</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>