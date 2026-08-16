<?php
// Path: resources/views/inventory/warehouses/index.php
$pageTitle = 'Warehouses & Locations';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Warehouses & Zones</h1>
        <p class="text-sm text-gray-500 mt-1">Manage physical storage locations, distribution centers, and bins.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50">
            <i class="fas fa-map-marker-alt mr-2"></i> Add Zone/Bin
        </button>
        <button class="btn-primary shadow-md">
            <i class="fas fa-plus mr-2"></i> Create Warehouse
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Warehouse Card 1 -->
    <div class="card p-0 overflow-hidden border-t-4 border-t-nour-primary">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-start">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">HQ - Main Riyadh Warehouse</h3>
                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-map-pin mr-1"></i> Industrial Area 2, Riyadh, KSA</p>
            </div>
            <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Active</span>
        </div>
        <div class="p-5 flex justify-between items-center text-sm">
            <div class="text-center">
                <p class="text-gray-500 text-xs mb-1">Total Value</p>
                <p class="font-bold text-gray-900">$1.2M</p>
            </div>
            <div class="text-center">
                <p class="text-gray-500 text-xs mb-1">Zones/Bins</p>
                <p class="font-bold text-gray-900">45</p>
            </div>
            <div class="text-center">
                <p class="text-gray-500 text-xs mb-1">Staff</p>
                <p class="font-bold text-gray-900">12</p>
            </div>
        </div>
        <div class="p-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
            <button class="text-sm font-medium text-gray-500 hover:text-nour-primary px-2 py-1">Edit Details</button>
            <button class="text-sm font-medium text-nour-primary hover:text-nour-dark px-2 py-1">View Stock</button>
        </div>
    </div>

    <!-- Warehouse Card 2 -->
    <div class="card p-0 overflow-hidden border-t-4 border-t-blue-500">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-start">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Jeddah Distribution Center</h3>
                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-map-pin mr-1"></i> Jeddah Port Zone, KSA</p>
            </div>
            <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Active</span>
        </div>
        <div class="p-5 flex justify-between items-center text-sm">
            <div class="text-center">
                <p class="text-gray-500 text-xs mb-1">Total Value</p>
                <p class="font-bold text-gray-900">$850K</p>
            </div>
            <div class="text-center">
                <p class="text-gray-500 text-xs mb-1">Zones/Bins</p>
                <p class="font-bold text-gray-900">22</p>
            </div>
            <div class="text-center">
                <p class="text-gray-500 text-xs mb-1">Staff</p>
                <p class="font-bold text-gray-900">8</p>
            </div>
        </div>
        <div class="p-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
            <button class="text-sm font-medium text-gray-500 hover:text-nour-primary px-2 py-1">Edit Details</button>
            <button class="text-sm font-medium text-nour-primary hover:text-nour-dark px-2 py-1">View Stock</button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>