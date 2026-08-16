<?php
// Path: resources/views/profile/sessions.php
$pageTitle = 'Active Sessions';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/profile" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Profile</a>
        <h1 class="text-2xl font-bold text-gray-800">Browser Sessions</h1>
        <p class="text-sm text-gray-500 mt-1">Manage and revoke your active sessions across other devices and browsers.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg shadow-md text-sm font-medium transition-colors" onclick="confirm('Are you sure you want to log out from all other devices?')">
            <i class="fas fa-sign-out-alt mr-2"></i> Log Out Other Devices
        </button>
    </div>
</div>

<div class="card p-0 max-w-4xl overflow-hidden">
    <div class="p-5 border-b border-gray-100 bg-gray-50">
        <p class="text-sm text-gray-600">If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.</p>
    </div>

    <div class="divide-y divide-gray-100">
        
        <!-- Current Session -->
        <div class="p-6 flex items-center gap-5 hover:bg-gray-50/50 transition-colors">
            <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-2xl flex-shrink-0">
                <i class="fas fa-desktop"></i>
            </div>
            <div class="flex-1">
                <p class="text-base font-bold text-gray-900">Windows - Google Chrome</p>
                <div class="flex items-center gap-3 mt-1 text-sm text-gray-500 font-medium">
                    <span>192.168.1.105</span>
                    <span>&bull;</span>
                    <span class="text-green-600 font-bold">This device</span>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs font-bold text-green-600 uppercase bg-green-50 px-2 py-1 rounded">Active Now</span>
            </div>
        </div>

        <!-- Another Session -->
        <div class="p-6 flex items-center gap-5 hover:bg-gray-50/50 transition-colors">
            <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-2xl flex-shrink-0">
                <i class="fas fa-mobile-alt"></i>
            </div>
            <div class="flex-1">
                <p class="text-base font-bold text-gray-900">iOS - Safari</p>
                <div class="flex items-center gap-3 mt-1 text-sm text-gray-500 font-medium">
                    <span>84.23.11.90</span>
                    <span>&bull;</span>
                    <span>Last active 2 hours ago</span>
                </div>
            </div>
            <div class="text-right">
                <button class="text-sm font-bold text-red-500 hover:text-red-700 transition-colors">Revoke</button>
            </div>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>