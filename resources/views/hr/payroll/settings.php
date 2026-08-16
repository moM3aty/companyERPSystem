<?php
// Path: resources/views/hr/payroll/settings.php
$pageTitle = 'Payroll Configuration';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><a href="/hr/payroll/dashboard" class="hover:text-nour-primary transition-colors">Payroll</a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Settings</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Salary Components & Rules</h1>
        <p class="text-sm text-gray-500 mt-1">Define allowances, deductions, and statutory formulas (e.g., GOSI, Taxes).</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md flex items-center">
            <i class="fas fa-plus mr-2"></i> New Component
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Allowances -->
    <div class="card p-0 overflow-hidden border-t-4 border-t-green-500">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800"><i class="fas fa-plus-circle text-green-500 mr-2"></i> Earnings & Allowances</h3>
        </div>
        <table class="w-full text-left border-collapse whitespace-nowrap text-sm">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-3 font-semibold">Component Name</th>
                    <th class="p-3 font-semibold">Calculation Type</th>
                    <th class="p-3 font-semibold text-center">Taxable</th>
                    <th class="p-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-bold text-gray-900">Basic Salary</td>
                    <td class="p-3 text-gray-600">Fixed Amount</td>
                    <td class="p-3 text-center text-green-500"><i class="fas fa-check"></i></td>
                    <td class="p-3 text-right"><button class="text-gray-400 hover:text-nour-primary"><i class="far fa-edit"></i></button></td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-bold text-gray-900">Housing Allowance</td>
                    <td class="p-3 text-gray-600 font-mono text-xs">25% of Basic</td>
                    <td class="p-3 text-center text-gray-300"><i class="fas fa-times"></i></td>
                    <td class="p-3 text-right"><button class="text-gray-400 hover:text-nour-primary"><i class="far fa-edit"></i></button></td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-bold text-gray-900">Transportation</td>
                    <td class="p-3 text-gray-600 font-mono text-xs">10% of Basic</td>
                    <td class="p-3 text-center text-gray-300"><i class="fas fa-times"></i></td>
                    <td class="p-3 text-right"><button class="text-gray-400 hover:text-nour-primary"><i class="far fa-edit"></i></button></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Deductions -->
    <div class="card p-0 overflow-hidden border-t-4 border-t-red-500">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800"><i class="fas fa-minus-circle text-red-500 mr-2"></i> Deductions & Taxes</h3>
        </div>
        <table class="w-full text-left border-collapse whitespace-nowrap text-sm">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-3 font-semibold">Component Name</th>
                    <th class="p-3 font-semibold">Formula / Rate</th>
                    <th class="p-3 font-semibold text-center">Mandatory</th>
                    <th class="p-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-bold text-gray-900">GOSI (Social Insurance)</td>
                    <td class="p-3 text-gray-600 font-mono text-xs">9.75% of (Basic + Housing)</td>
                    <td class="p-3 text-center text-red-500"><i class="fas fa-lock" title="Statutory"></i></td>
                    <td class="p-3 text-right"><button class="text-gray-400 hover:text-nour-primary"><i class="far fa-edit"></i></button></td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-bold text-gray-900">Unpaid Absence Penalty</td>
                    <td class="p-3 text-gray-600 font-mono text-xs">(Basic/30) * Absent Days</td>
                    <td class="p-3 text-center text-green-500"><i class="fas fa-check"></i></td>
                    <td class="p-3 text-right"><button class="text-gray-400 hover:text-nour-primary"><i class="far fa-edit"></i></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>