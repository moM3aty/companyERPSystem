<?php
// Path: resources/views/enterprise/advanced-hr/dashboard.php
$pageTitle = 'Talent & Performance Management';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Talent Management Hub</h1>
        <p class="text-sm text-gray-500 mt-1">Strategic overview of performance, succession planning, and competencies.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card p-5 border-l-4 border-l-nour-primary">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Top Performers (9-Box Grid)</p>
        <h3 class="text-3xl font-bold text-gray-900">12%</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">Of workforce in "High Potential/High Perf" box</p>
    </div>
    <div class="card p-5 border-l-4 border-l-yellow-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Retention Risk</p>
        <h3 class="text-3xl font-bold text-yellow-600">8</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">Key employees flagged for flight risk</p>
    </div>
    <div class="card p-5 border-l-4 border-l-green-500">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Training ROI</p>
        <h3 class="text-3xl font-bold text-green-600">+14%</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">Performance increase post-training</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Mockup 9-Box Grid in CSS -->
    <div class="card p-5">
        <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Talent Matrix (9-Box)</h3>
        <div class="relative w-full aspect-square max-w-sm mx-auto border-2 border-gray-800 flex flex-col">
            <!-- Y-Axis Label -->
            <div class="absolute -left-8 top-1/2 -translate-y-1/2 -rotate-90 text-xs font-bold text-gray-500 tracking-widest uppercase">Potential</div>
            <!-- X-Axis Label -->
            <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-bold text-gray-500 tracking-widest uppercase">Performance</div>

            <div class="flex-1 flex border-b border-gray-300">
                <div class="flex-1 border-r border-gray-300 bg-yellow-50 flex items-center justify-center p-2 text-center text-[10px] font-bold text-yellow-700">Enigma (5)</div>
                <div class="flex-1 border-r border-gray-300 bg-green-50 flex items-center justify-center p-2 text-center text-[10px] font-bold text-green-700">Growth (14)</div>
                <div class="flex-1 bg-green-100 flex items-center justify-center p-2 text-center text-xs font-black text-green-800 shadow-inner">Star (8)</div>
            </div>
            <div class="flex-1 flex border-b border-gray-300">
                <div class="flex-1 border-r border-gray-300 bg-red-50 flex items-center justify-center p-2 text-center text-[10px] font-bold text-red-700">Dilemma (12)</div>
                <div class="flex-1 border-r border-gray-300 bg-blue-50 flex items-center justify-center p-2 text-center text-[10px] font-bold text-blue-700">Core (45)</div>
                <div class="flex-1 bg-green-50 flex items-center justify-center p-2 text-center text-[10px] font-bold text-green-700">High Perf (22)</div>
            </div>
            <div class="flex-1 flex">
                <div class="flex-1 border-r border-gray-300 bg-red-100 flex items-center justify-center p-2 text-center text-xs font-black text-red-800 shadow-inner">Risk (6)</div>
                <div class="flex-1 border-r border-gray-300 bg-yellow-50 flex items-center justify-center p-2 text-center text-[10px] font-bold text-yellow-700">Inconsistent (18)</div>
                <div class="flex-1 bg-blue-50 flex items-center justify-center p-2 text-center text-[10px] font-bold text-blue-700">Solid (30)</div>
            </div>
        </div>
    </div>

    <!-- Succession Planning Alerts -->
    <div class="card p-0 overflow-hidden flex flex-col">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Critical Roles Succession</h3>
        </div>
        <div class="p-0 flex-1">
            <ul class="divide-y divide-gray-100">
                <li class="p-4 flex items-center justify-between hover:bg-gray-50">
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Chief Financial Officer (CFO)</p>
                        <p class="text-xs text-gray-500 mt-0.5">Incumbent: Sarah Smith (Retiring in 18 mo)</p>
                    </div>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-[10px] font-bold rounded">2 Ready Now</span>
                </li>
                <li class="p-4 flex items-center justify-between hover:bg-gray-50 bg-red-50/30">
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Lead ERP Architect</p>
                        <p class="text-xs text-gray-500 mt-0.5">Incumbent: Ahmed Hassan (High Flight Risk)</p>
                    </div>
                    <span class="px-2 py-1 bg-red-100 text-red-800 text-[10px] font-bold rounded">No Successor Identified</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>