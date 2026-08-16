<?php
// Path: resources/views/profile/edit.php
$pageTitle = 'Edit Profile';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Edit Profile</h1>
        <p class="text-sm text-gray-500 mt-1">Update your personal information and contact details.</p>
    </div>
</div>

<!-- Tabs -->
<div class="border-b border-gray-200 mb-6">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <a href="/profile" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
            <i class="far fa-user mr-2"></i> Overview
        </a>
        <a href="/profile/edit" class="border-nour-primary text-nour-primary whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
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

<div class="card p-6 max-w-3xl">
    <form action="/profile/update" method="POST" enctype="multipart/form-data">
        
        <!-- Avatar Upload -->
        <div class="flex items-center gap-6 mb-8">
            <div class="w-20 h-20 rounded-full border border-gray-200 overflow-hidden bg-gray-50">
                <img src="https://ui-avatars.com/api/?name=Admin+User&size=150&background=0a1930&color=fff" alt="Avatar" class="w-full h-full object-cover">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                <div class="flex gap-3">
                    <label class="bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded text-sm font-medium hover:bg-gray-50 transition-colors cursor-pointer shadow-sm">
                        <span>Change Photo</span>
                        <input type="file" name="avatar" class="hidden" accept="image/jpeg, image/png">
                    </label>
                    <button type="button" class="text-sm font-medium text-red-500 hover:text-red-700">Remove</button>
                </div>
                <p class="text-xs text-gray-500 mt-2">JPG, GIF or PNG. Max size of 2MB.</p>
            </div>
        </div>

        <hr class="border-gray-100 mb-6">

        <!-- Form Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                <input type="text" name="first_name" value="Admin" required class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50 text-gray-900">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                <input type="text" name="last_name" value="User" required class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50 text-gray-900">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" value="admin@nourtrust.com" disabled class="block w-full px-3 py-2 border border-gray-200 rounded-md sm:text-sm bg-gray-100 text-gray-500 cursor-not-allowed">
                <p class="text-[10px] text-gray-400 mt-1">Contact HR to change your official email.</p>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="tel" name="phone" value="+966 50 123 4567" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50 text-gray-900">
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <button type="submit" class="btn-primary shadow-md">Save Changes</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>