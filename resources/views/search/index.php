<?php
// Path: resources/views/search/index.php
$pageTitle = 'Global Search';
ob_start();
?>

<div class="max-w-4xl mx-auto mt-8">
    <div class="text-center mb-8">
        <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-tr from-nour-dark to-nour-primary flex items-center justify-center text-white text-2xl shadow-lg mb-4">
            <i class="fas fa-search"></i>
        </div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Search Everything</h1>
        <p class="text-gray-500">Find records across all ERP modules instantly.</p>
    </div>

    <!-- Main Search Bar -->
    <div class="relative mb-12 shadow-xl rounded-2xl group">
        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
            <i class="fas fa-search text-gray-400 text-xl group-focus-within:text-nour-primary transition-colors"></i>
        </div>
        <form action="/search/results" method="GET">
            <input type="text" name="q" placeholder="Type an invoice number, employee name, or product SKU..." 
                class="block w-full pl-14 pr-32 py-5 text-lg font-medium border-2 border-transparent rounded-2xl ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-nour-primary bg-white text-gray-900 shadow-sm transition-all focus:outline-none" autofocus autocomplete="off">
            
            <div class="absolute inset-y-2 right-2 flex gap-2">
                <button type="button" class="px-3 bg-gray-100 text-gray-500 font-bold rounded-xl hover:bg-gray-200 transition-colors text-xs uppercase tracking-widest hidden sm:block" title="Advanced Search">
                    <i class="fas fa-sliders-h"></i>
                </button>
                <button type="submit" class="px-8 bg-nour-primary text-white font-bold rounded-xl hover:bg-nour-dark transition-colors shadow-md">
                    Search
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Filters / Scopes -->
    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 text-center">Narrow Your Search</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
        <button class="p-5 bg-white border border-gray-200 rounded-2xl hover:border-blue-500 hover:shadow-md transition-all text-center group">
            <div class="w-12 h-12 mx-auto rounded-full bg-blue-50 text-blue-500 flex items-center justify-center mb-3 group-hover:bg-blue-500 group-hover:text-white transition-colors text-xl"><i class="fas fa-file-invoice-dollar"></i></div>
            <span class="text-sm font-bold text-gray-800">Financials</span>
        </button>
        <button class="p-5 bg-white border border-gray-200 rounded-2xl hover:border-green-500 hover:shadow-md transition-all text-center group">
            <div class="w-12 h-12 mx-auto rounded-full bg-green-50 text-green-500 flex items-center justify-center mb-3 group-hover:bg-green-500 group-hover:text-white transition-colors text-xl"><i class="fas fa-users"></i></div>
            <span class="text-sm font-bold text-gray-800">HR & People</span>
        </button>
        <button class="p-5 bg-white border border-gray-200 rounded-2xl hover:border-purple-500 hover:shadow-md transition-all text-center group">
            <div class="w-12 h-12 mx-auto rounded-full bg-purple-50 text-purple-500 flex items-center justify-center mb-3 group-hover:bg-purple-500 group-hover:text-white transition-colors text-xl"><i class="fas fa-boxes"></i></div>
            <span class="text-sm font-bold text-gray-800">Inventory</span>
        </button>
        <button class="p-5 bg-white border border-gray-200 rounded-2xl hover:border-orange-500 hover:shadow-md transition-all text-center group">
            <div class="w-12 h-12 mx-auto rounded-full bg-orange-50 text-orange-500 flex items-center justify-center mb-3 group-hover:bg-orange-500 group-hover:text-white transition-colors text-xl"><i class="fas fa-building"></i></div>
            <span class="text-sm font-bold text-gray-800">CRM & Sales</span>
        </button>
    </div>

    <!-- Recent Searches -->
    <div class="bg-transparent text-center">
        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4"><i class="fas fa-history mr-1"></i> Recent Searches</h3>
        <div class="flex flex-wrap justify-center gap-2">
            <a href="/search/results?q=Alpha" class="inline-flex items-center px-4 py-2 rounded-full bg-white border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-nour-primary transition-colors shadow-sm">
                Alpha <i class="fas fa-times ml-3 text-gray-300 hover:text-red-500"></i>
            </a>
            <a href="/search/results?q=INV-2026" class="inline-flex items-center px-4 py-2 rounded-full bg-white border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-nour-primary transition-colors shadow-sm">
                INV-2026 <i class="fas fa-times ml-3 text-gray-300 hover:text-red-500"></i>
            </a>
            <a href="/search/results?q=Ahmed" class="inline-flex items-center px-4 py-2 rounded-full bg-white border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-nour-primary transition-colors shadow-sm">
                Ahmed <i class="fas fa-times ml-3 text-gray-300 hover:text-red-500"></i>
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>