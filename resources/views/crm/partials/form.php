<?php
// Path: resources/views/crm/partials/form.php
/* Reusable Form Fields for Create/Edit Customer */
?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Company / Customer Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="<?= htmlspecialchars($customer['name'] ?? '') ?>" required class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
        <input type="email" name="email" value="<?= htmlspecialchars($customer['email'] ?? '') ?>" required class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number</label>
        <input type="tel" name="phone" value="<?= htmlspecialchars($customer['phone'] ?? '') ?>" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Billing Address</label>
        <textarea name="billing_address" rows="2" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm"><?= htmlspecialchars($customer['billing_address'] ?? '') ?></textarea>
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Tax / VAT Number</label>
        <input type="text" name="tax_number" value="<?= htmlspecialchars($customer['tax_number'] ?? '') ?>" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm font-mono">
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Credit Limit</label>
        <input type="number" step="0.01" name="credit_limit" value="<?= htmlspecialchars($customer['credit_limit'] ?? '0.00') ?>" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm text-right">
    </div>
</div>