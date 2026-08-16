<?php
// Path: resources/views/admin/branches/index.php
$pageTitle = 'Company Branches';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i></a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><a href="/admin" class="hover:text-nour-primary transition-colors">Settings</a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Branches</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Company Branches & Locations</h1>
        <p class="text-sm text-gray-500 mt-1">Manage physical retail stores, offices, and their assigned managers.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button class="btn-primary shadow-md flex items-center">
            <i class="fas fa-plus mr-2"></i> Add Branch
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Branch Card (HQ) -->
    <div class="card p-0 overflow-hidden border-t-4 border-t-nour-primary flex flex-col relative">
        <div class="absolute top-4 right-4 bg-nour-primary text-white text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded shadow-sm">Headquarters</div>
        <div class="p-6 flex-1">
            <h3 class="text-xl font-black text-gray-900 mb-1">HQ - Riyadh</h3>
            <p class="text-sm text-gray-500 mb-4"><i class="fas fa-map-marker-alt mr-1 text-gray-400"></i> King Fahd Road, Olaya District, Riyadh, KSA</p>
            
            <div class="space-y-2 text-sm text-gray-700">
                <div class="flex items-center"><i class="fas fa-phone w-5 text-gray-400"></i> +966 11 123 4567</div>
                <div class="flex items-center"><i class="fas fa-envelope w-5 text-gray-400"></i> hq@nourtrust.com</div>
                <div class="flex items-center mt-2 pt-2 border-t border-gray-100"><i class="fas fa-user-tie w-5 text-nour-primary"></i> <span class="font-medium text-gray-900 ml-1">Manager: Ahmed Yassin</span></div>
            </div>
        </div>
        <div class="bg-gray-50 p-3 border-t border-gray-100 flex justify-between items-center text-sm">
            <span class="text-gray-500 font-medium">84 Employees Assigned</span>
            <button class="text-nour-primary font-bold hover:underline">Edit Setup</button>
        </div>
    </div>

    <!-- Branch Card -->
    <div class="card p-0 overflow-hidden border-t-4 border-t-gray-300 flex flex-col relative">
        <div class="p-6 flex-1">
            <h3 class="text-xl font-black text-gray-900 mb-1">Jeddah Retail Branch</h3>
            <p class="text-sm text-gray-500 mb-4"><i class="fas fa-map-marker-alt mr-1 text-gray-400"></i> Tahlia Street, Jeddah, KSA</p>
            
            <div class="space-y-2 text-sm text-gray-700">
                <div class="flex items-center"><i class="fas fa-phone w-5 text-gray-400"></i> +966 12 987 6543</div>
                <div class="flex items-center"><i class="fas fa-envelope w-5 text-gray-400"></i> jeddah@nourtrust.com</div>
                <div class="flex items-center mt-2 pt-2 border-t border-gray-100"><i class="fas fa-user-tie w-5 text-nour-primary"></i> <span class="font-medium text-gray-900 ml-1">Manager: Omar Khalid</span></div>
            </div>
        </div>
        <div class="bg-gray-50 p-3 border-t border-gray-100 flex justify-between items-center text-sm">
            <span class="text-gray-500 font-medium">22 Employees Assigned</span>
            <button class="text-nour-primary font-bold hover:underline">Edit Setup</button>
        </div>
    </div>
    
    <!-- Branch Card -->
    <div class="card p-0 overflow-hidden border-t-4 border-t-gray-300 flex flex-col relative">
        <div class="p-6 flex-1">
            <h3 class="text-xl font-black text-gray-900 mb-1">Dammam Hub</h3>
            <p class="text-sm text-gray-500 mb-4"><i class="fas fa-map-marker-alt mr-1 text-gray-400"></i> First Industrial City, Dammam, KSA</p>
            
            <div class="space-y-2 text-sm text-gray-700">
                <div class="flex items-center"><i class="fas fa-phone w-5 text-gray-400"></i> +966 13 456 7890</div>
                <div class="flex items-center"><i class="fas fa-envelope w-5 text-gray-400"></i> dammam@nourtrust.com</div>
                <div class="flex items-center mt-2 pt-2 border-t border-gray-100"><i class="fas fa-user-tie w-5 text-gray-300"></i> <span class="italic text-gray-500 ml-1">No Manager Assigned</span></div>
            </div>
        </div>
        <div class="bg-gray-50 p-3 border-t border-gray-100 flex justify-between items-center text-sm">
            <span class="text-gray-500 font-medium">15 Employees Assigned</span>
            <button class="text-nour-primary font-bold hover:underline">Edit Setup</button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>