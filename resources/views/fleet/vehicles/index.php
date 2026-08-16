<?php
// Path: resources/views/fleet/vehicles/index.php
$pageTitle = 'Vehicles Register';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Vehicles Directory</h1>
        <p class="text-sm text-gray-500 mt-1">Manage company cars, delivery trucks, and heavy equipment.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="btn-primary shadow-md">
            <i class="fas fa-plus mr-2"></i> Add Vehicle
        </button>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-4 border-b border-gray-100 flex gap-4 bg-gray-50/50">
        <div class="relative flex-1 max-w-md">
            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
            <input type="text" placeholder="Search Plate No. or Make..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-nour-primary text-sm bg-white">
        </div>
    </div>
    
    <div class="table-responsive rounded-b-lg">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold w-16 text-center">Plate</th>
                    <th class="p-4 font-semibold">Make & Model</th>
                    <th class="p-4 font-semibold">Assigned Driver</th>
                    <th class="p-4 font-semibold text-right">Current Odometer</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 text-center">
                        <div class="border-2 border-gray-800 rounded bg-white px-2 py-1 font-bold text-gray-900 text-xs inline-block tracking-widest shadow-sm">
                            KSA 1024
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="font-bold text-gray-900 block">Isuzu NQR (Reefer)</span>
                        <span class="text-xs text-gray-500 font-mono">VIN: ISZM99882233</span>
                    </td>
                    <td class="p-4 font-medium text-nour-primary hover:underline cursor-pointer">Ahmed Zaki</td>
                    <td class="p-4 text-right font-bold text-gray-700">105,420 <span class="text-xs font-normal">KM</span></td>
                    <td class="p-4 text-center"><span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-green-200">Active</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-blue-500 px-2" title="Edit"><i class="far fa-edit"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4 text-center">
                        <div class="border-2 border-gray-800 rounded bg-white px-2 py-1 font-bold text-gray-900 text-xs inline-block tracking-widest shadow-sm">
                            DXB 8812
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="font-bold text-gray-900 block">Toyota Hilux</span>
                        <span class="text-xs text-gray-500 font-mono">VIN: TYT55441122</span>
                    </td>
                    <td class="p-4 text-gray-400 italic text-xs">Unassigned</td>
                    <td class="p-4 text-right font-bold text-gray-700">45,100 <span class="text-xs font-normal">KM</span></td>
                    <td class="p-4 text-center"><span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-yellow-200">Maintenance</span></td>
                    <td class="p-4 text-right">
                        <button class="text-gray-400 hover:text-blue-500 px-2" title="Edit"><i class="far fa-edit"></i></button>
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