<?php
// Path: resources/views/returns/index.php
$pageTitle = 'Returns Management (RMA)';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i> Home</a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Returns</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Returns & Refunds (RMA)</h1>
        <p class="text-sm text-gray-500 mt-1">Manage customer returns, supplier chargebacks, and defective items.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <a href="/returns/create" class="btn-primary shadow-md flex items-center">
            <i class="fas fa-undo-alt mr-2"></i> Process New Return
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card p-5 border-l-4 border-l-red-500 flex justify-between items-center">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pending RMAs</p>
            <h3 class="text-2xl font-bold text-gray-900">12</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-red-50 text-red-500 flex items-center justify-center text-lg"><i class="fas fa-box-open"></i></div>
    </div>
    <div class="card p-5 border-l-4 border-l-yellow-500 flex justify-between items-center">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Awaiting Inspection</p>
            <h3 class="text-2xl font-bold text-gray-900">5</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center text-lg"><i class="fas fa-search-plus"></i></div>
    </div>
    <div class="card p-5 border-l-4 border-l-green-500 flex justify-between items-center">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Refunded (MTD)</p>
            <h3 class="text-2xl font-bold text-gray-900">$8,450.00</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-lg"><i class="fas fa-hand-holding-usd"></i></div>
    </div>
</div>

<div class="card p-0 overflow-visible z-10 relative">
    <div class="p-4 border-b border-gray-100 flex gap-4 bg-gray-50/50">
        <input type="text" placeholder="Search RMA No. or Customer..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary w-64">
        <select class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-nour-primary text-gray-600">
            <option>All Returns</option>
            <option>Customer Returns (Sales)</option>
            <option>Supplier Returns (Purchasing)</option>
        </select>
    </div>
    <div class="table-responsive rounded-b-lg">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">RMA No.</th>
                    <th class="p-4 font-semibold">Date</th>
                    <th class="p-4 font-semibold">Customer / Supplier</th>
                    <th class="p-4 font-semibold">Original Ref.</th>
                    <th class="p-4 font-semibold text-right">Amount</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-nour-primary">RMA-2608-005</td>
                    <td class="p-4 text-gray-600">Aug 16, 2026</td>
                    <td class="p-4 font-medium text-gray-800">Alpha Tech Solutions</td>
                    <td class="p-4 text-xs font-mono text-gray-500">INV-2608-012</td>
                    <td class="p-4 text-right font-bold text-red-600">$1,200.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded text-[10px] font-bold uppercase">Pending Inspection</span></td>
                    <td class="p-4 text-right"><button class="text-gray-400 hover:text-nour-primary px-2"><i class="fas fa-eye"></i></button></td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-nour-primary">RMA-2608-004</td>
                    <td class="p-4 text-gray-600">Aug 14, 2026</td>
                    <td class="p-4 font-medium text-gray-800">Global IT Suppliers <span class="text-[10px] text-gray-400 block">(Supplier)</span></td>
                    <td class="p-4 text-xs font-mono text-gray-500">PO-2608-088</td>
                    <td class="p-4 text-right font-bold text-green-600">$3,500.00</td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-[10px] font-bold uppercase">Refunded</span></td>
                    <td class="p-4 text-right"><button class="text-gray-400 hover:text-nour-primary px-2"><i class="fas fa-eye"></i></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>