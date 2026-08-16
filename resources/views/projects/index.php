<?php
// Path: resources/views/projects/index.php
$pageTitle = 'Projects Management';
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <nav class="flex text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="/dashboard" class="hover:text-nour-primary transition-colors"><i class="fas fa-home mr-1"></i> Home</a>
                </li>
                <li><i class="fas fa-chevron-right text-xs mx-2"></i><span class="text-gray-800 font-medium">Projects</span></li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-800">Projects Portfolio</h1>
        <p class="text-sm text-gray-500 mt-1">Manage project lifecycles, budgets, and track overall progress.</p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
        <a href="/projects/create" class="btn-primary flex items-center shadow-md">
            <i class="fas fa-plus mr-2"></i> New Project
        </a>
    </div>
</div>

<div class="card p-4 mb-6 bg-white">
    <form action="/projects" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
        <div class="flex-1 w-full">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Search Project</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-search text-gray-400"></i></div>
                <input type="text" name="search" placeholder="Project Name or Code..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm bg-gray-50">
            </div>
        </div>
        <div class="w-full md:w-48">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</label>
            <select name="status" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-nour-primary sm:text-sm bg-gray-50">
                <option value="active">Active Projects</option>
                <option value="planned">Planned</option>
                <option value="completed">Completed</option>
                <option value="on_hold">On Hold</option>
            </select>
        </div>
        <div class="w-full md:w-auto">
            <button type="submit" class="w-full md:w-auto bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md border border-gray-300 text-sm font-medium transition-colors">
                Apply
            </button>
        </div>
    </form>
</div>

<div class="card p-0 overflow-visible relative">
    <div class="table-responsive rounded-lg">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="p-4 font-semibold">Project Name & Code</th>
                    <th class="p-4 font-semibold">Client</th>
                    <th class="p-4 font-semibold w-48">Progress</th>
                    <th class="p-4 font-semibold text-right">Budget</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4">
                        <a href="/projects/kanban" class="font-bold text-gray-900 group-hover:text-nour-primary block">ERP Implementation Phase 2</a>
                        <span class="text-xs text-gray-500 font-mono">PRJ-2026-001</span>
                    </td>
                    <td class="p-4 font-medium text-gray-700">Alpha Tech Solutions</td>
                    <td class="p-4">
                        <div class="flex justify-between text-[10px] font-bold text-nour-primary mb-1">
                            <span>65%</span>
                            <span>12/18 Tasks</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5"><div class="bg-nour-primary h-1.5 rounded-full" style="width: 65%"></div></div>
                    </td>
                    <td class="p-4 text-right">
                        <span class="font-bold text-gray-900">$45,000.00</span>
                        <p class="text-[10px] text-red-500 mt-0.5">Spent: $22,500.00</p>
                    </td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-[10px] uppercase font-bold">Active</span></td>
                    <td class="p-4 text-right">
                        <a href="/projects/kanban" class="text-gray-400 hover:text-nour-primary px-2" title="Board"><i class="fas fa-columns"></i></a>
                        <button class="text-gray-400 hover:text-blue-500 px-2" title="Edit"><i class="far fa-edit"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="p-4">
                        <a href="#" class="font-bold text-gray-900 group-hover:text-nour-primary block">Network Upgrade Infrastructure</a>
                        <span class="text-xs text-gray-500 font-mono">PRJ-2026-002</span>
                    </td>
                    <td class="p-4 font-medium text-gray-700">Internal (IT Dept)</td>
                    <td class="p-4">
                        <div class="flex justify-between text-[10px] font-bold text-yellow-600 mb-1">
                            <span>15%</span>
                            <span>2/20 Tasks</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5"><div class="bg-yellow-500 h-1.5 rounded-full" style="width: 15%"></div></div>
                    </td>
                    <td class="p-4 text-right">
                        <span class="font-bold text-gray-900">$120,000.00</span>
                        <p class="text-[10px] text-gray-500 mt-0.5">Spent: $5,000.00</p>
                    </td>
                    <td class="p-4 text-center"><span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-[10px] uppercase font-bold">Planned</span></td>
                    <td class="p-4 text-right">
                        <a href="#" class="text-gray-400 hover:text-nour-primary px-2" title="Board"><i class="fas fa-columns"></i></a>
                        <button class="text-gray-400 hover:text-blue-500 px-2" title="Edit"><i class="far fa-edit"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>