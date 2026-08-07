<?php
// المسار: app/views/purchase/index.php
$orders = $data['orders'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-invoice-dollar text-primary"></i> أوامر الشراء (PO)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">متابعة طلبات الشراء من الموردين واستلام البضائع.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/purchase/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إنشاء أمر شراء
    </a>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>رقم الأمر</th>
                        <th>المورد</th>
                        <th>التاريخ</th>
                        <th>المبلغ الإجمالي</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($orders)): foreach($orders as $order): 
                        $statusClass = match($order->status) {
                            'pending' => 'badge-warning',
                            'approved' => 'badge-info',
                            'ordered' => 'badge-primary',
                            'delivered' => 'badge-success',
                            'cancelled' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($order->status) {
                            'pending' => 'قيد الانتظار',
                            'approved' => 'معتمد',
                            'ordered' => 'تم الطلب / استلام جزئي',
                            'delivered' => 'مستلم بالكامل',
                            'cancelled' => 'ملغي',
                            default => $order->status
                        };
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($order->po_number); ?></td>
                        <td class="fw-bold"><i class="fas fa-truck-field text-muted"></i> <?php echo htmlspecialchars($order->supplier_name); ?></td>
                        <td class="text-muted font-monospace" style="font-size: 13px;"><?php echo date('Y-m-d', strtotime($order->created_at)); ?></td>
                        <td class="font-monospace fw-bold text-success" style="direction: ltr;"><?php echo number_format($order->total_amount, 2); ?></td>
                        <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <?php if (in_array($order->status, ['pending', 'approved', 'ordered'])) : ?>
                                    <a href="<?php echo URLROOT; ?>/purchase/receive/<?php echo $order->id; ?>" class="btn-icon view" title="استلام البضاعة"><i class="fas fa-box-open"></i></a>
                                <?php endif; ?>
                                <a href="<?php echo URLROOT; ?>/purchase/show/<?php echo $order->id; ?>" class="btn-icon" style="color:var(--text-muted); border-color:var(--border-color);" title="عرض التفاصيل"><i class="fas fa-eye"></i></a>
                                
                                <?php if ($order->status === 'pending'): ?>
                                    <a href="<?php echo URLROOT; ?>/purchase/edit/<?php echo $order->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                    <form action="<?php echo URLROOT; ?>/purchase/delete/<?php echo $order->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف أمر الشراء؟');">
                                        <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center text-muted" style="padding: 40px;">لا توجد أوامر شراء مسجلة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>