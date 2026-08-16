<?php
// Path: resources/views/crm/partials/table.php
/* Reusable Data Table for Customers */
?>
<div class="table-responsive rounded-t-lg">
    <table class="w-full text-left border-collapse whitespace-nowrap">
        <thead>
            <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                <th class="p-4 font-semibold w-8"><input type="checkbox" class="rounded border-gray-300 text-nour-primary"></th>
                <th class="p-4 font-semibold">Customer / Company</th>
                <th class="p-4 font-semibold">Contact Info</th>
                <th class="p-4 font-semibold">Industry</th>
                <th class="p-4 font-semibold text-center">Status</th>
                <th class="p-4 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
            <?php if(!empty($customers)): foreach($customers as $customer): ?>
            <tr class="hover:bg-gray-50/80 transition-colors group">
                <td class="p-4"><input type="checkbox" class="rounded border-gray-300 text-nour-primary"></td>
                <td class="p-4">
                    <a href="/crm/customers/<?= $customer['id'] ?>" class="font-semibold text-gray-900 group-hover:text-nour-primary transition-colors">
                        <?= htmlspecialchars($customer['name']) ?>
                    </a>
                </td>
                <td class="p-4">
                    <p class="text-xs text-gray-600"><i class="far fa-envelope mr-1"></i> <?= htmlspecialchars($customer['email']) ?></p>
                    <p class="text-xs text-gray-600 mt-0.5"><i class="fas fa-phone mr-1"></i> <?= htmlspecialchars($customer['phone']) ?></p>
                </td>
                <td class="p-4 text-gray-600 capitalize"><?= htmlspecialchars($customer['industry']) ?></td>
                <td class="p-4 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <?= htmlspecialchars($customer['status']) ?>
                    </span>
                </td>
                <td class="p-4 text-right">
                    <a href="/crm/customers/<?= $customer['id'] ?>/edit" class="text-gray-400 hover:text-blue-500 p-1"><i class="far fa-edit"></i></a>
                    <button class="text-gray-400 hover:text-red-500 p-1"><i class="far fa-trash-alt"></i></button>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr>
                <td colspan="6" class="p-6 text-center text-gray-400">No customers found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>