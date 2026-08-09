### 2️⃣ تحديث شاشة القائمة (لتأكيد قراءة الحالة مهما كان شكل الكلمة):
```php:app/views/salesOrder/index.php
<?php
// app/views/salesOrder/index.php
$orders =$data['orders'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-contract text-primary"></i> أوامر البيع (Sales Orders)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة وتتبع أوامر البيع قبل تحويلها إلى فواتير نهائية.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/salesOrder/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إنشاء أمر بيع جديد
    </a>
</div>

<?php 
    $flash = Session::getFlash();
    if ($flash): 
?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>">
        <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>رقم الأمر</th>
                        <th>العميل</th>
                        <th>التاريخ</th>
                        <th class="text-left">الإجمالي (ر.س)</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as$order) : 
                        // استخدام strtolower لضمان التطابق التام مهما كان شكل الكلمة في الداتابيز
                        $safeStatus = strtolower(trim($order->status ?? 'draft'));
                        
                        $statusClass = match($safeStatus) {
                            'approved' => 'badge-success',
                            'sent' => 'badge-info',
                            'cancelled' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($safeStatus) {
                            'approved' => 'معتمد',
                            'sent' => 'مُرسل',
                            'cancelled' => 'ملغي',
                            default => 'مسودة'
                        };
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($order->order_number); ?></td>
                        <td>
                            <div class="fw-bold text-primary"><i class="fas fa-user-circle text-muted"></i> <?php echo htmlspecialchars($order->customer_name ?? 'بدون اسم'); ?></div>
                        </td>
                        <td class="text-muted font-monospace fs-6">
                            <?php echo date('Y-m-d', strtotime($order->created_at)); ?>
                        </td>
                        <td class="font-monospace fw-bold text-success text-left" style="direction:ltr;">
                            <?php echo number_format($order->total_amount, 2); ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/salesOrder/show/<?php echo $order->id; ?>" class="btn-icon view" title="عرض وطباعة"><i class="fas fa-eye"></i></a>
                                <a href="<?php echo URLROOT; ?>/salesOrder/edit/<?php echo $order->id; ?>" class="btn-icon edit" title="تعديل الحالة والبيانات"><i class="fas fa-pen"></i></a>
                                <?php if(Session::hasRole('admin') || Session::hasRole('manager')): ?>
                                <form action="<?php echo URLROOT; ?>/salesOrder/delete/<?php echo $order->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف أمر البيع؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($orders)) : ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted p-5">
                            <i class="fas fa-file-signature fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد أوامر بيع مسجلة حالياً.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>