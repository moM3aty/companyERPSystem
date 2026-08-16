<?php
// Path: resources/views/enterprise/advanced-pricing/promotions.php
$pageTitle = 'Sales Promotions';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Promotions Engine</h1>
        <p class="text-sm text-gray-500 mt-1">Configure time-bound marketing promotions and automated cart discounts.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md">
            <i class="fas fa-bullhorn mr-2"></i> New Promotion
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Promo Card 1 -->
    <div class="card p-0 overflow-hidden border-t-4 border-t-green-500 flex flex-col">
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-bold text-gray-900 text-lg">Summer Tech Sale 2026</h3>
                <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Active</span>
            </div>
            <p class="text-xs text-gray-500"><i class="far fa-calendar-alt mr-1"></i> Ends in 14 days (Aug 31)</p>
        </div>
        <div class="p-5 flex-1">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-blue-100 text-nour-primary flex items-center justify-center font-bold text-lg">-15%</div>
                <div class="text-sm font-medium text-gray-800">Discount on all items in category: <span class="font-bold">Electronics</span></div>
            </div>
            <p class="text-xs text-gray-500 bg-gray-50 p-2 rounded border border-gray-100">Automatically applied at checkout. Stacks with volume discounts.</p>
        </div>
        <div class="p-3 border-t border-gray-100 flex justify-end gap-2 bg-white">
            <button class="text-gray-500 hover:text-nour-primary text-sm font-medium px-2 py-1 transition-colors">Edit</button>
            <button class="text-red-500 hover:text-red-700 text-sm font-medium px-2 py-1 transition-colors">Deactivate</button>
        </div>
    </div>

    <!-- Promo Card 2 -->
    <div class="card p-0 overflow-hidden border-t-4 border-t-nour-primary flex flex-col">
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-bold text-gray-900 text-lg">B2B Loyalty Coupon</h3>
                <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Coupon Required</span>
            </div>
            <p class="text-xs text-gray-500"><i class="fas fa-ticket-alt mr-1"></i> Code: B2B-VIP-100</p>
        </div>
        <div class="p-5 flex-1">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center font-bold text-sm">-$100</div>
                <div class="text-sm font-medium text-gray-800">Fixed cart discount if subtotal exceeds <span class="font-bold">$2,000.00</span></div>
            </div>
            <p class="text-xs text-gray-500 bg-gray-50 p-2 rounded border border-gray-100">Usage Limit: 50 | Used: 12</p>
        </div>
        <div class="p-3 border-t border-gray-100 flex justify-end gap-2 bg-white">
            <button class="text-gray-500 hover:text-nour-primary text-sm font-medium px-2 py-1 transition-colors">Edit</button>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>