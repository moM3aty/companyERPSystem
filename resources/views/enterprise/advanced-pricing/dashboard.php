<?php
// Path: resources/views/enterprise/advanced-pricing/dashboard.php
$pageTitle = 'Advanced Pricing Engine';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Advanced Pricing Rules</h1>
        <p class="text-sm text-gray-500 mt-1">Manage volume discounts, promotions, and customer-specific price lists.</p>
    </div>
    <button class="btn-primary shadow-md">
        <i class="fas fa-plus mr-2"></i> New Promotion
    </button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card p-0 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50"><h3 class="font-bold text-gray-800">Active Price Lists</h3></div>
        <table class="w-full text-left text-sm">
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 p-4 block">
                    <td>
                        <span class="font-bold text-nour-primary">VIP Wholesale 2026</span>
                        <p class="text-xs text-gray-500 mt-1">Applies to 45 Customers. Base discount 15%.</p>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 p-4 block">
                    <td>
                        <span class="font-bold text-nour-primary">Government B2G List</span>
                        <p class="text-xs text-gray-500 mt-1">Applies to 12 Customers. Pre-negotiated fixed rates.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50"><h3 class="font-bold text-gray-800">Active Promotions</h3></div>
        <table class="w-full text-left text-sm">
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 p-4 block flex justify-between items-center">
                    <div>
                        <span class="font-bold text-gray-800">Summer Clear-out</span>
                        <p class="text-xs text-gray-500 mt-1">20% off all Electronics.</p>
                    </div>
                    <span class="text-[10px] bg-green-100 text-green-800 px-2 py-1 rounded font-bold">Expires in 5 Days</span>
                </tr>
                <tr class="hover:bg-gray-50 p-4 block flex justify-between items-center">
                    <div>
                        <span class="font-bold text-gray-800">Buy 10 Get 1 Free</span>
                        <p class="text-xs text-gray-500 mt-1">Volume logic for Laptops.</p>
                    </div>
                    <span class="text-[10px] bg-blue-100 text-blue-800 px-2 py-1 rounded font-bold">Ongoing</span>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>