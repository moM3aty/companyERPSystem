<?php
// Path: resources/views/hr/employees/create.php
$pageTitle = 'Add New Employee';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/hr/employees" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Directory</a>
        <h1 class="text-2xl font-bold text-gray-800">Onboard New Employee</h1>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-3">
        <button type="submit" form="employeeForm" class="btn-primary px-8 shadow-md">Complete Onboarding</button>
    </div>
</div>

<form id="employeeForm" action="/api/v1/hr/employees" method="POST">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6 border-t-4 border-t-nour-primary">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" required class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" required class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">National ID / Passport <span class="text-red-500">*</span></label>
                        <input type="text" name="national_id" required class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Personal Phone</label>
                        <input type="tel" name="phone" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Job Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Department <span class="text-red-500">*</span></label>
                        <select name="department_id" required class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm bg-gray-50">
                            <option value="">Select Department...</option>
                            <option value="1">Information Technology</option>
                            <option value="2">Sales</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Direct Manager</label>
                        <select name="manager_id" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="">None (Top Level)</option>
                            <option value="10">Sarah Smith (IT Manager)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Job Title (Position)</label>
                        <select name="position_id" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="5">Senior Developer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Hire Date <span class="text-red-500">*</span></label>
                        <input type="date" name="hire_date" value="<?= date('Y-m-d') ?>" required class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm bg-gray-50">
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="card p-6 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">System Access</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Work Email (Creates User)</label>
                        <input type="email" name="email" placeholder="name@nourtrust.com" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                        <p class="text-[10px] text-gray-500 mt-1">Leaving this blank means no ERP login access.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Assign Role</label>
                        <select name="role_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="">Employee Self Service Only</option>
                            <option value="3">Sales Representative</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="card p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Initial Contract</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Basic Salary</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500">SAR</span></div>
                            <input type="number" step="0.01" name="basic_salary" value="0.00" required class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm font-bold text-gray-900">
                        </div>
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