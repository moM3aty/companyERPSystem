<?php
// Path: resources/views/profile/index.php
$pageTitle = 'My Profile';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i> Home</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-xs mx-2"></i>
                        <span class="text-gray-800 font-medium">My Profile</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Account Overview</h1>
        <p class="text-sm text-gray-500 mt-1">Manage your personal information and system preferences.</p>
    </div>
</div>

<div class="border-b border-gray-200 mb-6">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <a href="/profile" class="border-nour-primary text-nour-primary whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
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
        <a href="/profile/activity" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
            <i class="fas fa-history mr-2"></i> Activity Log
        </a>
    </nav>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left Column: User Card -->
    <div class="col-span-1 space-y-6">
        <div class="card p-6 text-center relative overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-nour-dark to-nour-primary"></div>
            
            <div class="relative z-10 mt-8">
                <div class="w-24 h-24 mx-auto rounded-full border-4 border-white shadow-lg overflow-hidden bg-white">
                    <img src="https://ui-avatars.com/api/?name=Admin+User&size=150&background=0a1930&color=fff" alt="Profile" class="w-full h-full object-cover">
                </div>
                <h2 class="text-xl font-bold text-gray-800 mt-4">Admin User</h2>
                <p class="text-sm text-nour-primary font-medium">System Administrator</p>
                
                <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span> Active Account
                </div>
            </div>

            <!-- Contact Info -->
            <div class="mt-6 pt-6 border-t border-gray-100 text-left">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Contact Information</h3>
                <ul class="space-y-3">
                    <li class="flex items-center text-sm text-gray-600">
                        <div class="w-8 h-8 rounded bg-gray-50 flex items-center justify-center text-gray-400 mr-3">
                            <i class="far fa-envelope"></i>
                        </div>
                        admin@nourtrust.com
                    </li>
                    <li class="flex items-center text-sm text-gray-600">
                        <div class="w-8 h-8 rounded bg-gray-50 flex items-center justify-center text-gray-400 mr-3">
                            <i class="fas fa-phone"></i>
                        </div>
                        +966 50 123 4567
                    </li>
                    <li class="flex items-center text-sm text-gray-600">
                        <div class="w-8 h-8 rounded bg-gray-50 flex items-center justify-center text-gray-400 mr-3">
                            <i class="far fa-building"></i>
                        </div>
                        HQ - Riyadh Branch
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-span-1 lg:col-span-2 space-y-6">
        
        <!-- Professional Information -->
        <div class="card p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Professional Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Employee ID</span>
                    <span class="text-sm text-gray-900 font-semibold">EMP-2024-001</span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Department</span>
                    <span class="text-sm text-gray-900 font-semibold">Information Technology</span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Direct Manager</span>
                    <span class="text-sm text-nour-primary font-medium hover:underline cursor-pointer">Ahmed Yassin</span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Date of Join</span>
                    <span class="text-sm text-gray-900 font-semibold">Jan 15, 2024</span>
                </div>
            </div>
        </div>

        <!-- System Preferences -->
        <div class="card p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">System Preferences</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Interface Language</span>
                    <span class="text-sm text-gray-900 font-semibold"><i class="fas fa-globe mr-1 text-gray-400"></i> English (US)</span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Timezone</span>
                    <span class="text-sm text-gray-900 font-semibold"><i class="far fa-clock mr-1 text-gray-400"></i> Asia/Riyadh (UTC+3)</span>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>