<?php
// Path: resources/views/enterprise/advanced-hr/career.php
$pageTitle = 'Career Path Planning';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Career Development Path</h1>
        <p class="text-sm text-gray-500 mt-1">Visualize and manage employee progression toward future target roles.</p>
    </div>
</div>

<div class="card p-6 mb-6 flex flex-col md:flex-row items-center gap-6 bg-white border-l-4 border-l-nour-primary">
    <img src="https://ui-avatars.com/api/?name=Zaid+M&background=0a1930&color=fff&size=128" class="w-20 h-20 rounded-full shadow-md border-2 border-gray-100">
    <div class="flex-1 text-center md:text-left">
        <h2 class="text-xl font-black text-gray-900">Zaid Mahmoud</h2>
        <p class="text-sm text-nour-primary font-bold">Current Role: Senior Developer (IT Dept)</p>
        <p class="text-xs text-gray-500 mt-1">Tenure: 3 Years | Performance Avg: 4.6/5</p>
    </div>
    <div class="text-right border-t md:border-t-0 md:border-l border-gray-200 pt-4 md:pt-0 md:pl-6 w-full md:w-auto">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Target Next Role</p>
        <h3 class="text-lg font-bold text-gray-900">Lead ERP Architect</h3>
        <p class="text-xs text-green-600 font-bold mt-1">Readiness: 75% (1-2 Years)</p>
    </div>
</div>

<div class="card p-6">
    <h3 class="text-base font-bold text-gray-800 mb-6 border-b border-gray-100 pb-2"><i class="fas fa-road mr-2 text-nour-primary"></i> Progression Timeline</h3>
    
    <div class="relative border-l-2 border-gray-200 ml-4 md:ml-6 space-y-8 pb-4">
        
        <!-- Timeline Item: Past -->
        <div class="relative pl-6 sm:pl-8">
            <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-gray-400 border-2 border-white shadow-sm"></div>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-1">
                <h4 class="text-sm font-bold text-gray-500 line-through">Junior Developer</h4>
                <time class="text-xs font-mono text-gray-400">Jan 2023 - Jan 2025</time>
            </div>
            <p class="text-xs text-gray-400">Entry role upon joining.</p>
        </div>

        <!-- Timeline Item: Current -->
        <div class="relative pl-6 sm:pl-8">
            <div class="absolute -left-[11px] top-1 w-5 h-5 rounded-full bg-nour-primary border-4 border-blue-100 shadow-sm animate-pulse"></div>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-1">
                <h4 class="text-base font-black text-nour-dark">Senior Developer</h4>
                <time class="text-xs font-mono font-bold text-nour-primary bg-blue-50 px-2 py-0.5 rounded">Present (Since Jan 2025)</time>
            </div>
            <p class="text-sm text-gray-600 mt-1">Currently leading backend modules and API integrations.</p>
            
            <!-- Development Plan Box -->
            <div class="mt-4 bg-gray-50 border border-gray-200 rounded p-4">
                <h5 class="text-xs font-bold text-gray-700 uppercase mb-3 border-b border-gray-200 pb-1">Development Plan (To reach Architect level)</h5>
                <ul class="space-y-3">
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                        <div>
                            <span class="font-medium text-gray-800 line-through text-gray-500">Complete 'Advanced Domain-Driven Design' Course</span>
                        </div>
                    </li>
                    <li class="flex items-start text-sm">
                        <i class="far fa-circle text-gray-300 mt-0.5 mr-2"></i>
                        <div>
                            <span class="font-medium text-gray-800">Lead a full module implementation independently</span>
                            <p class="text-[10px] text-yellow-600 font-bold mt-0.5">In Progress (Supply Chain Module)</p>
                        </div>
                    </li>
                    <li class="flex items-start text-sm">
                        <i class="far fa-circle text-gray-300 mt-0.5 mr-2"></i>
                        <div>
                            <span class="font-medium text-gray-800">Obtain AWS Certified Solutions Architect</span>
                            <button class="block text-[10px] text-nour-primary hover:underline mt-0.5 font-bold">Enroll in Training</button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Timeline Item: Future -->
        <div class="relative pl-6 sm:pl-8 opacity-60">
            <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full border-2 border-gray-300 bg-white"></div>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-1">
                <h4 class="text-lg font-bold text-gray-700">Lead ERP Architect</h4>
                <time class="text-xs font-mono text-gray-500">Target: Q1 2027</time>
            </div>
            <p class="text-sm text-gray-500 mt-1">Target position identified in Succession Planning.</p>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/app.php';
?>