<?php
// Path: resources/views/documents/common/approval-history.php
/*
 * Reusable Approval History (Audit Trail) for Internal Print Documents like POs and PRs
 * Expected Variables: $approvals (Array of approval steps)
 */
?>
<div class="mt-8 mb-4 page-break-inside-avoid">
    <h4 class="font-bold text-gray-800 uppercase tracking-wider mb-2 text-xs border-b border-gray-200 pb-1">
        Document Approval History (سجل الاعتمادات)
    </h4>
    <table class="w-full text-left text-xs border-collapse">
        <thead>
            <tr class="text-gray-500 border-b border-gray-200">
                <th class="py-2 pr-4 font-semibold">Action</th>
                <th class="py-2 pr-4 font-semibold">User / Role</th>
                <th class="py-2 pr-4 font-semibold">Date & Time</th>
                <th class="py-2 font-semibold">Comments</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <tr>
                <td class="py-2 pr-4 text-green-600 font-bold"><i class="fas fa-check-circle mr-1"></i> Final Approval</td>
                <td class="py-2 pr-4 font-medium text-gray-800">Sarah Smith (Finance Director)</td>
                <td class="py-2 pr-4 text-gray-500 font-mono">2026-08-15 10:45 AM</td>
                <td class="py-2 text-gray-600 italic">"Budget verified and approved."</td>
            </tr>
            <tr>
                <td class="py-2 pr-4 text-blue-600 font-bold"><i class="fas fa-clipboard-check mr-1"></i> Technical Review</td>
                <td class="py-2 pr-4 font-medium text-gray-800">Ahmed Hassan (IT Manager)</td>
                <td class="py-2 pr-4 text-gray-500 font-mono">2026-08-14 02:15 PM</td>
                <td class="py-2 text-gray-600 italic">"Specs meet our infrastructure requirements."</td>
            </tr>
            <tr>
                <td class="py-2 pr-4 text-gray-600 font-bold"><i class="fas fa-pen mr-1"></i> Document Created</td>
                <td class="py-2 pr-4 font-medium text-gray-800">Omar Khalid (Procurement)</td>
                <td class="py-2 pr-4 text-gray-500 font-mono">2026-08-14 09:00 AM</td>
                <td class="py-2 text-gray-600 italic">-</td>
            </tr>
        </tbody>
    </table>
</div>