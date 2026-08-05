<?php
$flash = $data['flash'] ?? null;
$transfers = $data['transfers'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="toolbar">
        <div class="toolbar-right"><h3>طلبات نقل المخزون</h3></div>
        <div>
            <a href="<?php echo URL_ROOT; ?>/warehouse/create-transfer" class="btn-add">
                <i class="fas fa-plus"></i> نقل جديد
            </a>
        </div>
    </div>

    <div class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>رقم النقل</th>
                        <th>المنتج</th>
                        <th>من مستودع</th>
                        <th>إلى مستودع</th>
                        <th>الكمية</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transfers as $tr) : ?>
                    <tr>
                        <td><?php echo $tr->transfer_number; ?></td>
                        <td><?php echo htmlspecialchars($tr->product_name); ?></td>
                        <td><?php echo htmlspecialchars($tr->from_warehouse_name); ?></td>
                        <td><?php echo htmlspecialchars($tr->to_warehouse_name); ?></td>
                        <td><?php echo $tr->quantity; ?></td>
                        <td>
                            <span class="badge badge-<?php echo $tr->status; ?>">
                                <?php echo ucfirst($tr->status); ?>
                            </span>
                        </td>
                        <td><?php echo $tr->created_at; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($transfers)) : ?>
                    <tr><td colspan="7" class="empty-state">لا توجد طلبات نقل</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>