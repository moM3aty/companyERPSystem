<?php
$flash = $data['flash'] ?? null;
$orders = $data['orders'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="toolbar">
        <div class="toolbar-right"><h3>أوامر الشراء</h3></div>
        <div>
            <a href="<?php echo URL_ROOT; ?>/purchase/create" class="btn-add">
                <i class="fas fa-plus"></i> أمر شراء جديد
            </a>
        </div>
    </div>

    <div class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>رقم الأمر</th>
                        <th>المورد</th>
                        <th>الإجمالي</th>
                        <th>التاريخ</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order) : ?>
                    <tr>
                        <td><?php echo $order->po_number; ?></td>
                        <td><?php echo htmlspecialchars($order->supplier_name); ?></td>
                        <td style="direction:ltr;"><?php echo number_format($order->total_amount, 2); ?></td>
                        <td><?php echo $order->created_at; ?></td>
                        <td>
                            <span class="badge badge-<?php echo $order->status; ?>">
                                <?php echo ucfirst($order->status); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($order->status == 'pending' || $order->status == 'approved') : ?>
                                <a href="<?php echo URL_ROOT; ?>/purchase/receive/<?php echo $order->id; ?>" class="act-btn btn-success" title="استلام">
                                    <i class="fas fa-check-double"></i>
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo URL_ROOT; ?>/purchase/view/<?php echo $order->id; ?>" class="act-btn btn-view"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($orders)) : ?>
                    <tr><td colspan="6" class="empty-state">لا توجد أوامر شراء</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>