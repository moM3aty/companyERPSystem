<?php
// Path: resources/views/admin/settings/general.php
$pageTitle = 'General Settings';
ob_start();
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">System Settings</h1>
    <p class="text-sm text-gray-500 mt-1">Configure core company parameters, localization, and system defaults.</p>
</div>

<!-- Tabs -->
<div class="border-b border-gray-200 mb-6">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <a href="/admin/settings/general" class="border-nour-primary text-nour-primary whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            <i class="fas fa-building mr-2"></i> Company Info
        </a>
        <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
            <i class="fas fa-globe mr-2"></i> Localization
        </a>
        <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
            <i class="fas fa-file-invoice-dollar mr-2"></i> Financial Settings
        </a>
    </nav>
</div>

<form action="/api/v1/settings/general" method="POST">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        
        <!-- Company Identity -->
        <div class="card p-6 md:col-span-2">
            <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Corporate Identity</h3>
            
            <div class="flex items-center gap-6 mb-6">
                <div class="w-24 h-24 rounded border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center text-gray-400 flex-col cursor-pointer hover:bg-gray-100 transition-colors">
                    <i class="fas fa-cloud-upload-alt text-xl mb-1"></i>
                    <span class="text-[10px] font-medium">Upload Logo</span>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company Legal Name <span class="text-red-500">*</span></label>
                    <input type="text" value="Nour Trust Enterprise" required class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm bg-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tax / VAT Number <span class="text-red-500">*</span></label>
                    <input type="text" value="300123456789003" required class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Commercial Register (CR)</label>
                    <input type="text" value="1010123456" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm font-mono">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">HQ Address</label>
                    <textarea rows="2" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm">King Fahd Road, Riyadh, Saudi Arabia</textarea>
                </div>
            </div>
        </div>

        <!-- Localization Summary -->
        <div class="card p-6 bg-gray-50">
            <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">Active Localization</h3>
            <div class="space-y-4 text-sm">
                <div>
                    <p class="text-gray-500 text-xs font-semibold uppercase">Base Currency</p>
                    <p class="font-bold text-gray-900 mt-0.5">SAR (Saudi Riyal)</p>
                    <p class="text-[10px] text-red-500 mt-1 italic"><i class="fas fa-lock"></i> Cannot be changed after transactions exist.</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-semibold uppercase">Timezone</p>
                    <p class="font-bold text-gray-900 mt-0.5">Asia/Riyadh (UTC+3)</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-semibold uppercase">Fiscal Year End</p>
                    <p class="font-bold text-gray-900 mt-0.5">December 31st</p>
                </div>
            </div>
        </div>

    </div>

    <div class="flex justify-end pt-4 border-t border-gray-200">
        <button type="submit" class="btn-primary shadow-lg px-8">Save Configuration</button>
    </div>
</form>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>