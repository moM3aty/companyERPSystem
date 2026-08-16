<?php
// Path: resources/views/crm/edit.php
$pageTitle = 'Edit Customer';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/crm/customers" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Customers</a>
        <h1 class="text-2xl font-bold text-gray-800">Edit: Alpha Tech Solutions</h1>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-3">
        <button type="button" onclick="window.history.back()" class="bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">Cancel</button>
        <button type="submit" form="editCustomerForm" class="btn-primary px-8 shadow-md flex items-center">
            <i class="fas fa-save mr-2"></i> Save Changes
        </button>
    </div>
</div>

<form id="editCustomerForm" action="/api/v1/crm/customers/1" method="POST">
    <!-- Method Spoofing for PUT request -->
    <input type="hidden" name="_method" value="PUT">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6 border-t-4 border-t-nour-primary">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Business Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Company Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="Alpha Tech Solutions" required class="block w-full px-4 py-2.5 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Primary Contact</label>
                        <input type="text" name="contact_person" value="Sarah Jenkins" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Industry</label>
                        <select name="industry" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm">
                            <option value="technology" selected>Technology & IT</option>
                            <option value="manufacturing">Manufacturing</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="sarah@alphatech.com" required class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Phone</label>
                        <input type="tel" name="phone" value="+966 50 123 4567" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Billing Address</label>
                        <textarea name="billing_address" rows="3" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm">456 Innovation Park, Jeddah 54321</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Settings -->
        <div class="lg:col-span-1 space-y-6">
            <div class="card p-6 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">Account Settings</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                        <select name="status" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="lead">Lead</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">VAT Number</label>
                        <input type="text" name="tax_number" value="300987654321003" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm font-mono text-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Credit Limit</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500">$</span></div>
                            <input type="number" step="0.01" name="credit_limit" value="50000.00" class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm text-right font-bold text-gray-900">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Price List</label>
                        <select name="price_list_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="">Standard Retail</option>
                            <option value="1" selected>VIP Wholesale (15% Off)</option>
                        </select>
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