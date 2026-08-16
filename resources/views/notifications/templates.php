<?php
// Path: resources/views/notifications/templates.php
$pageTitle = 'Notification Templates';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Communication Templates</h1>
        <p class="text-sm text-gray-500 mt-1">Configure automated Email and SMS messages sent by the ERP system.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Categories Sidebar -->
    <div class="lg:col-span-1 space-y-2">
        <button class="w-full text-left px-4 py-3 bg-nour-primary text-white font-bold rounded-lg shadow-md flex justify-between items-center">
            <span>Sales & Invoices</span>
        </button>
        <button class="w-full text-left px-4 py-3 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
            Purchasing (PO, RFQ)
        </button>
        <button class="w-full text-left px-4 py-3 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
            Human Resources
        </button>
        <button class="w-full text-left px-4 py-3 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
            System Alerts
        </button>
    </div>

    <!-- Template Editor -->
    <div class="lg:col-span-3">
        <div class="card p-0 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Template: Invoice Issued to Customer</h3>
                <span class="bg-green-100 text-green-800 text-[10px] uppercase font-bold px-2 py-1 rounded">Active</span>
            </div>
            <form class="p-5 space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email Subject</label>
                    <input type="text" value="New Invoice [Invoice_No] from Nour Trust" class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm font-medium">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email Body (HTML/Text)</label>
                    <textarea rows="8" class="w-full border-gray-300 rounded-md focus:ring-nour-primary text-sm font-mono p-3 leading-relaxed">
Dear [Customer_Name],

Thank you for your business. Please find attached your invoice [Invoice_No] for the amount of [Grand_Total] [Currency].

Due Date: [Due_Date]

If you have any questions, please contact us.

Best Regards,
Nour Trust Finance Team
                    </textarea>
                    <div class="mt-2 text-xs text-gray-500 bg-blue-50 p-2 rounded border border-blue-100">
                        <span class="font-bold text-blue-800">Available Variables:</span> [Customer_Name], [Invoice_No], [Grand_Total], [Currency], [Due_Date]
                    </div>
                </div>
                
                <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-50 shadow-sm">Send Test Email</button>
                    <button type="button" class="btn-primary shadow-md">Save Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>