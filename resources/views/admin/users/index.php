<?php
// Path: resources/views/admin/users/index.php
$pageTitle = 'Users Management';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span>Settings</span></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Users</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">System Users</h1>
        <p class="text-sm text-gray-500 mt-1">Manage login access, roles, and branch assignments.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <a href="/admin/roles" class="bg-white border border-gray-300 text-gray-700 px-3 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            <i class="fas fa-user-shield mr-2"></i> Roles & Permissions
        </a>
        <button class="btn-primary shadow-md flex items-center">
            <i class="fas fa-user-plus mr-2"></i> Invite User
        </button>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <input type="text" placeholder="Search user..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-64">
        <div class="flex text-sm text-gray-500 font-medium gap-4">
            <span>Total: <span class="text-gray-900 font-bold">45</span></span>
            <span>Active: <span class="text-green-600 font-bold">42</span></span>
        </div>
    </div>
    <div class="table-responsive rounded-b-lg">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">User</th>
                    <th class="p-4 font-semibold">Role</th>
                    <th class="p-4 font-semibold">Default Branch</th>
                    <th class="p-4 font-semibold">Last Login</th>
                    <th class="p-4 font-semibold text-center">2FA Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                <!-- Admin User -->
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=Admin+User&background=0a1930&color=fff" class="w-8 h-8 rounded-full shadow-sm border border-gray-200">
                            <div>
                                <span class="block font-bold text-gray-900 leading-tight">Admin User</span>
                                <span class="block text-[10px] text-gray-500">admin@nourtrust.com</span>
                            </div>
                        </div>
                    </td>
                    <td class="p-4"><span class="bg-nour-dark text-white px-2 py-0.5 rounded text-[10px] font-bold uppercase">Super Admin</span></td>
                    <td class="p-4 text-gray-600 font-medium">All Branches</td>
                    <td class="p-4 text-gray-500 text-xs">Just now</td>
                    <td class="p-4 text-center text-green-500" title="Enabled"><i class="fas fa-shield-check text-lg"></i></td>
                    <td class="p-4 text-right text-gray-300">
                        <button disabled title="Cannot edit yourself here"><i class="fas fa-lock"></i></button>
                    </td>
                </tr>

                <!-- Standard User -->
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=Sarah+Smith&background=f59e0b&color=fff" class="w-8 h-8 rounded-full shadow-sm border border-gray-200">
                            <div>
                                <span class="block font-bold text-gray-900 leading-tight group-hover:text-nour-primary transition-colors cursor-pointer">Sarah Smith</span>
                                <span class="block text-[10px] text-gray-500">s.smith@nourtrust.com</span>
                            </div>
                        </div>
                    </td>
                    <td class="p-4"><span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-blue-200">Finance Manager</span></td>
                    <td class="p-4 text-gray-600 font-medium">HQ - Riyadh</td>
                    <td class="p-4 text-gray-500 text-xs">2 hours ago</td>
                    <td class="p-4 text-center text-gray-300" title="Disabled"><i class="fas fa-shield-alt text-lg"></i></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-blue-500 px-1"><i class="far fa-edit"></i></button>
                        <button class="text-gray-400 hover:text-red-500 px-1 ml-1"><i class="fas fa-ban"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>