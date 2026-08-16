<?php
// Path: resources/views/crm/create.php
$pageTitle = 'Add New Customer';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/crm/customers" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Customers</a>
        <h1 class="text-2xl font-bold text-gray-800">Add New Customer</h1>
        <p class="text-sm text-gray-500 mt-1">Register a new client or lead into the CRM system.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-3">
        <a href="/crm/customers" class="bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
            Cancel
        </a>
        <button type="submit" form="createCustomerForm" class="btn-primary px-8 shadow-md">
            Save Customer
        </button>
    </div>
</div>

<form id="createCustomerForm" action="/api/v1/crm/customers" method="POST">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column (Main Info) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Basic Information -->
            <div class="card p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="far fa-id-card mr-2 text-nour-primary"></i> Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Company / Customer Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="e.g., Alpha Tech Solutions" class="block w-full px-4 py-2.5 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Primary Contact Person</label>
                        <input type="text" name="contact_person" placeholder="e.g., Sarah Jenkins" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Industry Sector</label>
                        <select name="industry" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm">
                            <option value="">Select Industry...</option>
                            <option value="technology">Technology & IT</option>
                            <option value="manufacturing">Manufacturing</option>
                            <option value="retail">Retail & E-commerce</option>
                            <option value="healthcare">Healthcare</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Contact & Address -->
            <div class="card p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="far fa-address-book mr-2 text-nour-primary"></i> Contact & Address Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required placeholder="info@company.com" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" required placeholder="+1 (555) 000-0000" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Billing Address</label>
                        <textarea name="billing_address" rows="2" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">City</label>
                        <input type="text" name="city" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Country</label>
                        <select name="country_id" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary focus:border-nour-primary sm:text-sm">
                            <option value="SA">Saudi Arabia</option>
                            <option value="AE">United Arab Emirates</option>
                            <option value="EG">Egypt</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column (Financial & Settings) -->
        <div class="lg:col-span-1 space-y-6">
            
            <div class="card p-6 bg-gray-50 border-t-4 border-t-nour-primary">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2"><i class="fas fa-file-invoice-dollar mr-2 text-nour-primary"></i> Financial Settings</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tax / VAT Number</label>
                        <input type="text" name="tax_number" placeholder="Optional" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm font-mono text-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Currency</label>
                        <select name="currency_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="1">SAR - Saudi Riyal</option>
                            <option value="2">USD - US Dollar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Credit Limit</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" step="0.01" name="credit_limit" value="0.00" class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm text-right">
                        </div>
                        <p class="text-[10px] text-gray-500 mt-1">Set to 0 for unlimited or strict cash-only.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Price List (Tier)</label>
                        <select name="price_list_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="">Standard Retail Pricing</option>
                            <option value="1">VIP Wholesale (15% Off)</option>
                            <option value="2">B2G Government List</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fas fa-tags mr-2 text-nour-primary"></i> Segmentation</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Customer Status</label>
                        <select name="status" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="active">Active Customer</option>
                            <option value="lead">Lead / Prospect</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Assigned Sales Rep</label>
                        <select name="sales_rep_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
                            <option value="">Unassigned</option>
                            <option value="10">Omar Khalid (Sales Manager)</option>
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