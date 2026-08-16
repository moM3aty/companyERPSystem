<?php
// Path: resources/views/fleet/dashboard.php
$pageTitle = 'Fleet Management Dashboard';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center"><a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i> Home</a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1"></i><span class="text-gray-800 font-medium">Fleet Management</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Fleet & Delivery Operations</h1>
        <p class="text-sm text-gray-500 mt-1">Track company vehicles, driver assignments, trips, and fuel consumption.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="card p-5 border-l-4 border-l-nour-primary flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Active Vehicles</p>
            <h4 class="text-2xl font-bold text-gray-900">42</h4>
        </div>
        <div class="w-10 h-10 rounded-full bg-blue-50 text-nour-primary flex items-center justify-center text-lg"><i class="fas fa-truck"></i></div>
    </div>
    <div class="card p-5 border-l-4 border-l-yellow-500 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">In-Transit Trips</p>
            <h4 class="text-2xl font-bold text-gray-900">14</h4>
        </div>
        <div class="w-10 h-10 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center text-lg"><i class="fas fa-route"></i></div>
    </div>
    <div class="card p-5 border-l-4 border-l-red-500 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Maintenance Due</p>
            <h4 class="text-2xl font-bold text-red-600">3</h4>
        </div>
        <div class="w-10 h-10 rounded-full bg-red-50 text-red-500 flex items-center justify-center text-lg"><i class="fas fa-tools"></i></div>
    </div>
    <div class="card p-5 border-l-4 border-l-green-500 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Fuel Cost (MTD)</p>
            <h4 class="text-2xl font-bold text-gray-900">$8,450</h4>
        </div>
        <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-lg"><i class="fas fa-gas-pump"></i></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Active Trips Map/List -->
    <div class="lg:col-span-2 card p-0 overflow-hidden flex flex-col">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-base font-bold text-gray-800"><i class="fas fa-map-marker-alt text-red-500 mr-2"></i> Live Fleet Tracking (Simulated)</h3>
            <a href="/fleet/trips" class="text-sm font-medium text-nour-primary hover:underline">View All Trips</a>
        </div>
        <div class="flex-1 min-h-[300px] bg-gray-200 relative flex items-center justify-center">
            <!-- Simulated Map Background -->
            <img src="https://placehold.co/800x400/e2e8f0/94a3b8?text=Interactive+Fleet+Map" alt="Map" class="w-full h-full object-cover opacity-50 mix-blend-multiply">
            
            <!-- Pin 1 -->
            <div class="absolute top-[40%] left-[30%] flex flex-col items-center animate-bounce">
                <div class="bg-nour-dark text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-lg mb-1">TRK-101</div>
                <i class="fas fa-map-pin text-3xl text-red-600 drop-shadow-md"></i>
            </div>
            
            <!-- Pin 2 -->
            <div class="absolute top-[60%] right-[40%] flex flex-col items-center">
                <div class="bg-nour-dark text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-lg mb-1">TRK-089</div>
                <i class="fas fa-map-pin text-3xl text-nour-primary drop-shadow-md"></i>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Alerts -->
    <div class="lg:col-span-1 space-y-6">
        <div class="card p-5">
            <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Quick Dispatch</h3>
            <button class="w-full btn-primary py-2.5 mb-3 shadow-md"><i class="fas fa-paper-plane mr-2"></i> Start New Trip</button>
            <button class="w-full bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 py-2.5 rounded-md font-medium shadow-sm"><i class="fas fa-gas-pump mr-2 text-gray-400"></i> Log Fuel Receipt</button>
        </div>

        <div class="card p-0 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-red-50">
                <h3 class="text-base font-bold text-red-800"><i class="fas fa-bell mr-2"></i> Fleet Alerts</h3>
            </div>
            <ul class="divide-y divide-gray-100 text-sm">
                <li class="p-4">
                    <p class="font-bold text-gray-800">License Expiry Warning</p>
                    <p class="text-xs text-gray-500 mt-1">Driver 'Ahmed Z.' license expires in 5 days.</p>
                </li>
                <li class="p-4">
                    <p class="font-bold text-gray-800">Odometer Milestone</p>
                    <p class="text-xs text-gray-500 mt-1">Vehicle 'TRK-101' crossed 100,000 KM. PM required.</p>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>