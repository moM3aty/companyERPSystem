<?php
// Path: resources/views/enterprise/intercompany/matching.php
$pageTitle = 'Matching Rules Engine';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/enterprise/intercompany/dashboard" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Dashboard</a>
        <h1 class="text-2xl font-bold text-gray-800">Matching Rules Engine</h1>
        <p class="text-sm text-gray-500 mt-1">Configure AI and rule-based tolerances for automated intercompany reconciliation.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md">
            <i class="fas fa-plus mr-2"></i> Add Rule
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="card p-0 overflow-hidden">
            <div class="p-4 bg-gray-50 border-b border-gray-200"><h3 class="font-bold text-gray-800">Active Tolerance Rules</h3></div>
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-white text-gray-500 text-xs uppercase border-b border-gray-200">
                    <tr>
                        <th class="p-4 font-semibold">Rule Name</th>
                        <th class="p-4 font-semibold">Criteria</th>
                        <th class="p-4 font-semibold text-center">Tolerance</th>
                        <th class="p-4 font-semibold text-center">Auto-Clear</th>
                        <th class="p-4 font-semibold w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 font-bold text-gray-900">Exchange Rate Fluctuation</td>
                        <td class="p-4 text-gray-600">Match by Ref No. AND Date within 3 days</td>
                        <td class="p-4 text-center font-bold text-yellow-600">≤ $5.00</td>
                        <td class="p-4 text-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" checked class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                        </td>
                        <td class="p-4 text-center"><button class="text-gray-400 hover:text-nour-primary"><i class="fas fa-edit"></i></button></td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 font-bold text-gray-900">Goods In Transit Delay</td>
                        <td class="p-4 text-gray-600">AR Date < AP Date (Max 14 days)</td>
                        <td class="p-4 text-center font-bold text-gray-500">Exact Amount</td>
                        <td class="p-4 text-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" checked class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                        </td>
                        <td class="p-4 text-center"><button class="text-gray-400 hover:text-nour-primary"><i class="fas fa-edit"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="card p-6 bg-gray-800 text-white">
            <h3 class="text-lg font-bold mb-4 border-b border-gray-600 pb-2"><i class="fas fa-robot text-nour-light mr-2"></i> AI Auto-Match Settings</h3>
            <p class="text-sm text-gray-300 mb-6">Let the system intelligently map unmatched records based on historical reconciliation patterns.</p>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium">Enable Fuzzy String Matching</span>
                    <input type="checkbox" checked class="rounded text-nour-light bg-gray-700 border-gray-600 focus:ring-nour-light">
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium">Confidence Threshold</span>
                    <span class="text-sm font-bold text-nour-light">95%</span>
                </div>
                <input type="range" min="80" max="100" value="95" class="w-full h-1 bg-gray-600 rounded-lg appearance-none cursor-pointer">
            </div>

            <button class="w-full mt-6 bg-nour-primary hover:bg-nour-light text-white py-2 rounded font-bold transition-colors">
                Save AI Preferences
            </button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>