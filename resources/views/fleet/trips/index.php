<?php
// Path: resources/views/fleet/trips/index.php
$pageTitle = 'Trip Log & Dispatch';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Trip Log & Dispatch</h1>
        <p class="text-sm text-gray-500 mt-1">Monitor vehicle movements, distances covered, and associated costs.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md">
            <i class="fas fa-paper-plane mr-2"></i> Dispatch Vehicle
        </button>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="table-responsive">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Trip Ref</th>
                    <th class="p-4 font-semibold">Vehicle & Driver</th>
                    <th class="p-4 font-semibold">Route (From <i class="fas fa-arrow-right mx-1"></i> To)</th>
                    <th class="p-4 font-semibold text-right">Distance</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-blue-50/20 transition-colors group">
                    <td class="p-4 font-bold text-nour-primary">TRP-0815-01</td>
                    <td class="p-4">
                        <span class="font-bold text-gray-800">KSA 1024</span>
                        <span class="text-xs text-gray-500 block">Driver: Ahmed Zaki</span>
                    </td>
                    <td class="p-4 text-gray-700">HQ Riyadh <i class="fas fa-long-arrow-alt-right mx-2 text-gray-400"></i> Jeddah Branch</td>
                    <td class="p-4 text-right text-gray-400">-</td>
                    <td class="p-4 text-center"><span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-blue-200">In Progress</span></td>
                    <td class="p-4 text-right">
                        <button class="bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1 rounded text-xs font-bold transition-colors">Complete Trip</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 font-bold text-gray-600">TRP-0814-44</td>
                    <td class="p-4">
                        <span class="font-bold text-gray-800">DXB 8812</span>
                        <span class="text-xs text-gray-500 block">Driver: Omar Ali</span>
                    </td>
                    <td class="p-4 text-gray-700">Jeddah Port <i class="fas fa-long-arrow-alt-right mx-2 text-gray-400"></i> Main Warehouse</td>
                    <td class="p-4 text-right font-bold text-gray-900">45 KM</td>
                    <td class="p-4 text-center"><span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-gray-200">Completed</span></td>
                    <td class="p-4 text-right">
                        <span class="text-xs font-medium text-gray-400"><i class="fas fa-check"></i> Logged</span>
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