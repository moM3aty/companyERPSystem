<?php
// Path: resources/views/notifications/index.php
$pageTitle = 'Notification Center';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Notification Center</h1>
        <p class="text-sm text-gray-500 mt-1">Stay updated with system alerts, approvals, and messages.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-3">
        <a href="/profile/notifications" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors flex items-center">
            <i class="fas fa-cog mr-2 text-gray-400"></i> Settings
        </a>
        <button class="bg-gray-100 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-200 transition-colors flex items-center" onclick="showToast('All notifications marked as read.')">
            <i class="fas fa-check-double mr-2 text-gray-500"></i> Mark All as Read
        </button>
    </div>
</div>

<div class="card flex flex-col md:flex-row overflow-hidden min-h-[600px]">
    
    <!-- Sidebar Filters -->
    <div class="w-full md:w-64 bg-gray-50 border-r border-gray-200 p-5 flex flex-col">
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Filters</h3>
        
        <ul class="space-y-1">
            <li>
                <a href="#" class="flex items-center justify-between px-3 py-2 rounded-md bg-white text-nour-primary font-medium shadow-sm border border-gray-200">
                    <span><i class="fas fa-inbox mr-2 w-4 text-center"></i> All Inbox</span>
                    <span class="bg-nour-primary text-white text-[10px] px-2 py-0.5 rounded-full">12</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-between px-3 py-2 rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                    <span><i class="fas fa-circle text-nour-light mr-2 w-4 text-center text-xs"></i> Unread</span>
                    <span class="text-gray-400 text-xs font-medium">3</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-between px-3 py-2 rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                    <span><i class="fas fa-check-circle text-gray-400 mr-2 w-4 text-center"></i> Read</span>
                </a>
            </li>
        </ul>

        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-8 mb-4">Categories</h3>
        <ul class="space-y-1">
            <li>
                <a href="#" class="flex items-center px-3 py-2 rounded-md text-gray-600 hover:bg-gray-100 transition-colors">
                    <span class="w-2 h-2 rounded-full bg-red-500 mr-3"></span> System Alerts
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center px-3 py-2 rounded-md text-gray-600 hover:bg-gray-100 transition-colors">
                    <span class="w-2 h-2 rounded-full bg-yellow-500 mr-3"></span> Approvals
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center px-3 py-2 rounded-md text-gray-600 hover:bg-gray-100 transition-colors">
                    <span class="w-2 h-2 rounded-full bg-blue-500 mr-3"></span> Operations
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center px-3 py-2 rounded-md text-gray-600 hover:bg-gray-100 transition-colors">
                    <span class="w-2 h-2 rounded-full bg-green-500 mr-3"></span> HR & Payroll
                </a>
            </li>
        </ul>
    </div>

    <div class="flex-1 bg-white flex flex-col">
        <!-- List Header -->
        <div class="px-6 py-3 border-b border-gray-100 flex items-center justify-between bg-white z-10 sticky top-0">
            <div class="flex items-center gap-3">
                <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                <span class="text-sm font-medium text-gray-700">Select All</span>
            </div>
            <div class="flex gap-2">
                <button class="text-gray-400 hover:text-gray-700 px-2 py-1 transition-colors" title="Delete Selected"><i class="far fa-trash-alt"></i></button>
                <button class="text-gray-400 hover:text-gray-700 px-2 py-1 transition-colors" title="Mark Selected as Read"><i class="far fa-envelope-open"></i></button>
            </div>
        </div>

        <!-- List Items -->
        <div class="flex-1 overflow-y-auto">
            <div class="divide-y divide-gray-100">
                
                <!-- Unread Notification (Approval) -->
                <div class="p-4 sm:px-6 hover:bg-blue-50/30 transition-colors bg-blue-50/10 flex items-start gap-4 group cursor-pointer">
                    <div class="mt-1">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </div>
                    <div class="w-10 h-10 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="text-sm font-bold text-gray-900 truncate">Purchase Order Requires Approval</h4>
                            <span class="text-xs font-semibold text-nour-primary whitespace-nowrap ml-2"><i class="fas fa-circle text-[8px] mr-1"></i> Just now</span>
                        </div>
                        <p class="text-sm text-gray-600 line-clamp-2">Purchase Order #PO-2026-0045 submitted by Ahmed Ali for IT Equipment requires your approval before processing.</p>
                        <div class="mt-3 flex gap-2">
                            <button class="px-3 py-1.5 bg-nour-primary text-white text-xs font-medium rounded shadow-sm hover:bg-nour-dark transition-colors">Review Document</button>
                        </div>
                    </div>
                </div>

                <!-- Unread Notification (System Error) -->
                <div class="p-4 sm:px-6 hover:bg-red-50/30 transition-colors bg-red-50/10 flex items-start gap-4 group cursor-pointer">
                    <div class="mt-1">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </div>
                    <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="text-sm font-bold text-gray-900 truncate">ZATCA Integration Sync Failed</h4>
                            <span class="text-xs font-semibold text-nour-primary whitespace-nowrap ml-2"><i class="fas fa-circle text-[8px] mr-1"></i> 2 hours ago</span>
                        </div>
                        <p class="text-sm text-gray-600 line-clamp-2">The automated synchronization of B2B invoices with the ZATCA portal failed due to a network timeout. The system will retry in 1 hour.</p>
                    </div>
                </div>

                <!-- Read Notification (Payroll) -->
                <div class="p-4 sm:px-6 hover:bg-gray-50 transition-colors flex items-start gap-4 group cursor-pointer opacity-70">
                    <div class="mt-1">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </div>
                    <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="text-sm font-semibold text-gray-700 truncate">Payslip Generated</h4>
                            <span class="text-xs text-gray-400 whitespace-nowrap ml-2">Yesterday</span>
                        </div>
                        <p class="text-sm text-gray-500 line-clamp-2">Your payslip for the period of August 2026 has been generated and is available for download in the ESS portal.</p>
                    </div>
                </div>

                <!-- Read Notification (Inventory) -->
                <div class="p-4 sm:px-6 hover:bg-gray-50 transition-colors flex items-start gap-4 group cursor-pointer opacity-70">
                    <div class="mt-1">
                        <input type="checkbox" class="rounded border-gray-300 text-nour-primary focus:ring-nour-primary">
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="text-sm font-semibold text-gray-700 truncate">Low Stock Alert: Laptop Pro X</h4>
                            <span class="text-xs text-gray-400 whitespace-nowrap ml-2">2 days ago</span>
                        </div>
                        <p class="text-sm text-gray-500 line-clamp-2">The inventory level for Product 'Laptop Pro X' in the Main Warehouse has fallen below the reorder point. Current stock: 5 units.</p>
                    </div>
                </div>

            </div>
            
            <!-- Load More -->
            <div class="p-4 text-center border-t border-gray-100">
                <button class="text-sm font-medium text-nour-primary hover:text-nour-dark transition-colors">Load Older Notifications</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>