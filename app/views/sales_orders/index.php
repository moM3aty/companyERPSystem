<?php
// المسار: app/views/sales_orders/index.php
$orders = $data['orders'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-contract text-primary"></i> أوامر البيع (Sales Orders)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">متابعة طلبات العملاء وإدارتها.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/salesOrder/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إنشاء أمر بيع
    </a>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>رقم الأمر</th>
                        <th>العميل</th>
                        <th>التاريخ</th>
                        <th>المبلغ الإجمالي</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($orders)): foreach($orders as $order): 
                        $statusClass = match($order->status) {
                            'draft' => 'badge-secondary',
                            'confirmed' => 'badge-info',
                            'invoiced' => 'badge-success',
                            'canceled' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($order->status) {
                            'draft' => 'مسودة', 'confirmed' => 'مؤكد', 'invoiced' => 'مُفوتر', 'canceled' => 'ملغي', default => $order->status
                        };
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($order->order_number); ?></td>
                        <td class="fw-bold"><i class="fas fa-user-circle text-muted"></i> <?php echo htmlspecialchars($order->customer_name); ?></td>
                        <td class="text-muted font-monospace" style="font-size: 13px;"><?php echo date('Y-m-d', strtotime($order->order_date)); ?></td>
                        <td class="font-monospace fw-bold text-success" style="direction: ltr;"><?php echo number_format($order->total_amount, 2); ?></td>
                        <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/salesOrder/show/<?php echo $order->id; ?>" class="btn-icon view" title="التفاصيل"><i class="fas fa-eye"></i></a>
                                <?php if ($order->status === 'draft'): ?>
                                    <a href="<?php echo URLROOT; ?>/salesOrder/edit/<?php echo $order->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                    <form action="<?php echo URLROOT; ?>/salesOrder/delete/<?php echo $order->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد الحذف؟');">
                                        <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center text-muted" style="padding: 40px;">لا توجد أوامر بيع مسجلة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>