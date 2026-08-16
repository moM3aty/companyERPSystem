<?php
// Path: resources/views/returns/edit.php
$pageTitle = 'Edit Return (RMA)';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="/returns" class="text-xs text-nour-primary hover:underline font-semibold uppercase tracking-wider mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Returns</a>
        <h1 class="text-2xl font-bold text-gray-800">Edit RMA: <span class="text-nour-primary">RMA-2608-005</span></h1>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-3">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50">Cancel</button>
        <button class="btn-primary shadow-md">Update RMA</button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="card p-5">
            <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Inspection & Condition</h3>
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4 text-sm text-yellow-800 flex items-start gap-3">
                <i class="fas fa-exclamation-triangle mt-1"></i>
                <p>This return is pending inspection. Please verify the condition of the items before authorizing the refund to inventory or scrap.</p>
            </div>
            
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="text-gray-500 border-b border-gray-200">
                        <th class="pb-2 font-semibold">Product</th>
                        <th class="pb-2 font-semibold text-center w-24">Returned Qty</th>
                        <th class="pb-2 font-semibold w-48">Inspection Result</th>
                        <th class="pb-2 font-semibold text-center w-32">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100 py-3">
                        <td class="py-3">
                            <span class="font-bold text-gray-800">Dell PowerEdge R740</span>
                            <p class="text-[10px] text-gray-500 mt-0.5">Reported: Damaged in transit</p>
                        </td>
                        <td class="py-3 text-center font-bold text-gray-900">1</td>
                        <td class="py-3 pr-4">
                            <select class="w-full border-gray-300 rounded focus:ring-nour-primary text-sm py-1.5">
                                <option value="damaged" selected>Confirmed Damaged (Scrap)</option>
                                <option value="good">Good Condition (Restock)</option>
                                <option value="rejected">Reject Return (Send Back)</option>
                            </select>
                        </td>
                        <td class="py-3 text-center">
                            <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded">To Scrap Account</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="lg:col-span-1">
        <div class="card p-5 bg-gray-50">
            <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">Financial Impact</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Refund Amount:</span>
                    <span class="font-bold text-gray-900">$1,200.00</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Restocking Fee (10%):</span>
                    <span class="font-bold text-red-600">-$120.00</span>
                </div>
                <div class="pt-2 border-t border-gray-200 mt-2 flex justify-between items-center font-bold text-lg">
                    <span>Credit to Customer:</span>
                    <span class="text-nour-dark">$1,080.00</span>
                </div>
            </div>
            <button class="w-full btn-primary mt-6 py-2.5 bg-green-600 hover:bg-green-700 border-none shadow-sm flex justify-center items-center">
                <i class="fas fa-check-double mr-2"></i> Approve & Issue Credit Note
            </button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>