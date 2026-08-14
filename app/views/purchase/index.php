<?php
// app/views/purchase/index.php
$purchases = $data['purchases'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-invoice text-primary"></i> أوامر الشراء (Purchase Orders)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة ومتابعة طلبات الشراء الموجهة للموردين لتعزيز المخزون.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/purchase/create" class="btn btn-primary fw-bold"><i class="fas fa-plus"></i> إنشاء أمر شراء جديد (PO)</a>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?> mb-3"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 align-middle text-center table-hover">
            <thead class="bg-light">
                <tr>
                    <th>رقم الأمر (PO)</th>
                    <th>تاريخ الطلب</th>
                    <th>المورد</th>
                    <th class="text-left">الإجمالي (SAR)</th>
                    <th>الحالة</th>
                    <th>بواسطة</th>
                    <th class="d-print-none">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($purchases as $p): 
                    $badgeClass = 'bg-secondary';
                    if ($p->status == 'Approved') $badgeClass = 'bg-primary';
                    elseif ($p->status == 'Received') $badgeClass = 'bg-success';
                ?>
                <tr>
                    <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($p->order_number); ?></td>
                    <td class="font-monospace text-muted"><?php echo $p->order_date; ?></td>
                    <td class="fw-bold"><i class="fas fa-truck text-muted me-1"></i> <?php echo htmlspecialchars($p->supplier_name ?? 'مورد محذوف/غير معروف'); ?></td>
                    <td class="font-monospace fw-black text-danger fs-5 text-left" style="direction:ltr; text-align: left;">
                        <?php echo number_format($p->total_amount, 2); ?>
                    </td>
                    <td><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($p->status); ?></span></td>
                    <td><small class="text-muted"><?php echo htmlspecialchars($p->user_name); ?></small></td>
                    <td class="d-print-none">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="<?php echo URLROOT; ?>/purchase/show/<?php echo $p->id; ?>" class="btn-icon view text-primary" title="عرض الفاتورة والطباعة"><i class="fas fa-eye"></i></a>
                            
                            <?php if(Session::hasRole('admin') || Session::hasRole('super_admin') || Session::hasRole('manager')): ?>
                            <form action="<?php echo URLROOT; ?>/purchase/delete/<?php echo $p->id; ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف أمر الشراء نهائياً؟');">
                                <button type="submit" class="btn-icon delete text-danger" style="border:none; background:none;" title="حذف أمر الشراء"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; if(empty($purchases)): ?>
                    <tr><td colspan="7" class="text-center text-muted p-5"><i class="fas fa-shopping-cart fs-1 opacity-25 mb-3 d-block"></i>لا توجد أوامر شراء مسجلة حالياً.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>