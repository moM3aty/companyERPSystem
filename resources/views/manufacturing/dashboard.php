<?php
// Path: resources/views/manufacturing/dashboard.php
$pageTitle = 'Manufacturing Dashboard';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Manufacturing (MRP)</h1>
        <p class="text-sm text-gray-500 mt-1">Monitor production orders, work centers, and bill of materials.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <a href="/manufacturing/production-orders/create" class="btn-primary shadow-md">
            <i class="fas fa-industry mr-2"></i> New Production Order
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="card p-5 border-l-4 border-l-blue-500 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Active Orders</p>
            <h3 class="text-2xl font-bold text-gray-900">14</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-lg"><i class="fas fa-cogs"></i></div>
    </div>
    <div class="card p-5 border-l-4 border-l-yellow-500 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Material Shortages</p>
            <h3 class="text-2xl font-bold text-yellow-600">3</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center text-lg"><i class="fas fa-exclamation-triangle"></i></div>
    </div>
    <div class="card p-5 border-l-4 border-l-green-500 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Produced Today</p>
            <h3 class="text-2xl font-bold text-gray-900">1,250</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-lg"><i class="fas fa-box-check"></i></div>
    </div>
    <div class="card p-5 border-l-4 border-l-purple-500 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Work Centers Load</p>
            <h3 class="text-2xl font-bold text-gray-900">85%</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-lg"><i class="fas fa-industry"></i></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card p-0 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Production Queue</h3>
            <a href="/manufacturing/work-orders" class="text-xs text-nour-primary font-medium hover:underline">View All</a>
        </div>
        <table class="w-full text-left text-sm whitespace-nowrap">
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-bold text-nour-primary">PROD-001</td>
                    <td class="p-4 text-gray-800">Server Rack 42U</td>
                    <td class="p-4 text-center"><span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold">In Progress</span></td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-bold text-gray-600">PROD-002</td>
                    <td class="p-4 text-gray-800">Network Cable 5m</td>
                    <td class="p-4 text-center"><span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-[10px] font-bold">Waiting Mat.</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Work Center Status</h3>
        </div>
        <table class="w-full text-left text-sm whitespace-nowrap">
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-medium text-gray-900">Assembly Line A</td>
                    <td class="p-4 text-center">
                        <span class="text-green-500 font-bold text-xs"><i class="fas fa-circle mr-1"></i> Running</span>
                    </td>
                    <td class="p-4 text-right text-xs text-gray-500">OEE: 92%</td>
                </tr>
                <tr class="hover:bg-gray-50 bg-red-50/20">
                    <td class="p-4 font-medium text-gray-900">CNC Machine 1</td>
                    <td class="p-4 text-center">
                        <span class="text-red-500 font-bold text-xs"><i class="fas fa-tools mr-1"></i> Maintenance</span>
                    </td>
                    <td class="p-4 text-right text-xs text-gray-500">OEE: 45%</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>