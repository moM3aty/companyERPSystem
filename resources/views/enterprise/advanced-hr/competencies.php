<?php
// Path: resources/views/enterprise/advanced-hr/competencies.php
$pageTitle = 'Competency Framework';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Competency Framework</h1>
        <p class="text-sm text-gray-500 mt-1">Map required skills to job positions and identify employee skill gaps.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50">
            <i class="fas fa-project-diagram mr-2 text-nour-light"></i> Skill Matrix
        </button>
        <button class="btn-primary shadow-md">
            <i class="fas fa-plus mr-2"></i> Add Skill
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 space-y-4">
        <div class="card p-0 overflow-hidden">
            <div class="p-4 bg-nour-dark text-white font-bold border-b border-gray-700">Skill Categories</div>
            <ul class="divide-y divide-gray-100 text-sm">
                <li class="p-3 hover:bg-gray-50 cursor-pointer font-medium border-l-4 border-l-nour-primary bg-blue-50/30">Technical & IT (12)</li>
                <li class="p-3 hover:bg-gray-50 cursor-pointer text-gray-600 border-l-4 border-transparent">Leadership & Management (8)</li>
                <li class="p-3 hover:bg-gray-50 cursor-pointer text-gray-600 border-l-4 border-transparent">Communication & Soft Skills (5)</li>
            </ul>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="card p-0 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Category: Technical & IT</h3>
            </div>
            <div class="p-0">
                <table class="w-full text-left text-sm border-collapse whitespace-nowrap">
                    <thead class="bg-white text-gray-500 text-xs uppercase border-b border-gray-200">
                        <tr>
                            <th class="p-4 font-semibold">Skill / Competency</th>
                            <th class="p-4 font-semibold text-center">Required By</th>
                            <th class="p-4 font-semibold text-center">Avg. Emp Level</th>
                            <th class="p-4 font-semibold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 font-bold text-gray-900">Database Architecture</td>
                            <td class="p-4 text-center text-xs font-medium text-gray-500">4 Positions</td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-1 text-nour-primary text-xs">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i><i class="far fa-star text-gray-300"></i>
                                </div>
                            </td>
                            <td class="p-4 text-right"><button class="text-gray-400 hover:text-nour-primary"><i class="fas fa-cog"></i></button></td>
                        </tr>
                        <tr class="hover:bg-gray-50 bg-red-50/20">
                            <td class="p-4 font-bold text-gray-900">ZATCA API Integration <span class="text-[10px] text-red-500 font-black ml-1 uppercase">Gap Detected</span></td>
                            <td class="p-4 text-center text-xs font-medium text-gray-500">2 Positions</td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-1 text-yellow-500 text-xs">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star text-gray-300"></i><i class="far fa-star text-gray-300"></i><i class="far fa-star text-gray-300"></i>
                                </div>
                            </td>
                            <td class="p-4 text-right"><button class="text-xs font-bold text-nour-primary hover:underline">Recommend Training</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/app.php';
?>