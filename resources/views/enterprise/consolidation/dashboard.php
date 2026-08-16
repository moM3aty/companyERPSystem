<?php
// Path: resources/views/enterprise/consolidation/dashboard.php
$pageTitle = 'Multi-Company Consolidation';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Financial Consolidation</h1>
        <p class="text-sm text-gray-500 mt-1">Group level financial statements and intercompany eliminations.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="btn-primary shadow-md">
            <i class="fas fa-sync-alt mr-2"></i> Run Consolidation
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Group Structure -->
    <div class="lg:col-span-1 space-y-4">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Group Structure</h3>
        <div class="card p-4">
            <ul class="space-y-3">
                <li class="flex items-center text-sm font-bold text-nour-dark"><i class="fas fa-sitemap mr-2 text-nour-primary"></i> Nour Trust Holdings</li>
                <li class="flex items-center text-sm ml-6 text-gray-700"><i class="fas fa-building mr-2 text-gray-400"></i> Nour Enterprise KSA (100%)</li>
                <li class="flex items-center text-sm ml-6 text-gray-700"><i class="fas fa-building mr-2 text-gray-400"></i> Nour Logistics UAE (80%)</li>
                <li class="flex items-center text-sm ml-6 text-gray-700"><i class="fas fa-building mr-2 text-gray-400"></i> Alpha Manufacturing (51%)</li>
            </ul>
        </div>
    </div>

    <!-- Consolidated Financials -->
    <div class="lg:col-span-3">
        <div class="card p-0 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex justify-between bg-gray-50">
                <h3 class="font-bold text-gray-800">Consolidated Income Statement (YTD)</h3>
                <span class="text-xs bg-nour-primary/10 text-nour-primary px-2 py-1 rounded font-bold">Base Currency: USD</span>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-200">
                    <tr>
                        <th class="p-3">Account</th>
                        <th class="p-3 text-right">Nour KSA</th>
                        <th class="p-3 text-right">Nour UAE</th>
                        <th class="p-3 text-right text-red-500">Eliminations</th>
                        <th class="p-3 text-right font-bold text-nour-dark">Consolidated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="p-3 font-medium">Revenue</td>
                        <td class="p-3 text-right">1,200,000</td>
                        <td class="p-3 text-right">800,000</td>
                        <td class="p-3 text-right text-red-500">-150,000</td>
                        <td class="p-3 text-right font-bold">1,850,000</td>
                    </tr>
                    <tr>
                        <td class="p-3 font-medium">COGS</td>
                        <td class="p-3 text-right">-600,000</td>
                        <td class="p-3 text-right">-450,000</td>
                        <td class="p-3 text-right text-red-500">150,000</td>
                        <td class="p-3 text-right font-bold">-900,000</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="p-3 font-bold text-nour-primary">Gross Profit</td>
                        <td class="p-3 text-right font-bold">600,000</td>
                        <td class="p-3 text-right font-bold">350,000</td>
                        <td class="p-3 text-right font-bold text-red-500">0</td>
                        <td class="p-3 text-right font-bold text-nour-dark text-base">950,000</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>