<?php
// Path: resources/views/profile/security.php
$pageTitle = 'Security Settings';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Security & Privacy</h1>
        <p class="text-sm text-gray-500 mt-1">Manage your password, two-factor authentication, and active sessions.</p>
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
        <a href="/profile/security" class="border-nour-primary text-nour-primary whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            <i class="fas fa-shield-alt mr-2"></i> Security & 2FA
        </a>
        <a href="/profile/notifications" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
            <i class="fas fa-bell mr-2"></i> Notifications
        </a>
        <a href="/profile/activity" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
            <i class="fas fa-history mr-2"></i> Activity Log
        </a>
    </nav>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left Column: Change Password & 2FA -->
    <div class="col-span-1 space-y-6">
        
        <!-- Change Password Card -->
        <div class="card p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Change Password</h3>
            <form action="/profile/security/password" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input type="password" name="current_password" required class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="new_password" required class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50">
                    <p class="text-xs text-gray-500 mt-1">Must be at least 8 characters long.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" required class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50">
                </div>
                <button type="submit" class="btn-primary w-full">Update Password</button>
            </form>
        </div>

        <!-- 2FA Card -->
        <div class="card p-6 border-l-4 border-l-nour-light">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Two-Factor Auth (2FA)</h3>
            <p class="text-sm text-gray-600 mb-4">Add an extra layer of security to your account. We recommend using an authenticator app.</p>
            
            <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
                <div>
                    <p class="text-sm font-medium text-gray-900">Authenticator App</p>
                    <p class="text-xs text-gray-500">Not configured</p>
                </div>
                <button class="px-3 py-1.5 border border-gray-300 rounded text-sm font-medium hover:bg-gray-50 transition-colors">Setup</button>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">SMS Verification</p>
                    <p class="text-xs text-green-600 font-medium"><i class="fas fa-check-circle mr-1"></i> Active (+966 *** **67)</p>
                </div>
                <button class="px-3 py-1.5 border border-red-200 text-red-600 rounded text-sm font-medium hover:bg-red-50 transition-colors">Disable</button>
            </div>
        </div>
    </div>

    <div class="col-span-1 lg:col-span-2">
        <div class="card p-0 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Active Sessions</h3>
                    <p class="text-xs text-gray-500 mt-1">Devices currently logged into your account.</p>
                </div>
                <button class="text-red-600 text-sm font-medium hover:underline bg-red-50 px-3 py-1.5 rounded border border-red-100 transition-colors">Revoke All Other Sessions</button>
            </div>
            
            <div class="divide-y divide-gray-100">
                <!-- Current Session -->
                <div class="p-5 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-nour-primary flex items-center justify-center text-lg">
                            <i class="fas fa-desktop"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Windows - Google Chrome <span class="ml-2 text-[10px] uppercase bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold">This Device</span></p>
                            <p class="text-xs text-gray-500 mt-0.5">IP: 192.168.1.105 &bull; Riyadh, SA</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-gray-900">Active Now</p>
                    </div>
                </div>

                <!-- Other Session -->
                <div class="p-5 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-lg">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">iPhone 14 Pro - Safari</p>
                            <p class="text-xs text-gray-500 mt-0.5">IP: 84.23.11.90 &bull; Jeddah, SA</p>
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end gap-2">
                        <p class="text-xs text-gray-500">Last active: 2 hours ago</p>
                        <button class="text-xs text-red-500 hover:text-red-700 font-medium transition-colors">Revoke Access</button>
                    </div>
                </div>

                <!-- Another Session -->
                <div class="p-5 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-lg">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">MacBook Pro - Firefox</p>
                            <p class="text-xs text-gray-500 mt-0.5">IP: 45.12.8.22 &bull; Dubai, AE</p>
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end gap-2">
                        <p class="text-xs text-gray-500">Last active: 3 days ago</p>
                        <button class="text-xs text-red-500 hover:text-red-700 font-medium transition-colors">Revoke Access</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>