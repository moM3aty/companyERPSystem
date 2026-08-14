<?php
$orders = $data['orders'] ?? [];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-shopping-cart text-primary"></i> أوامر البيع (Sales Orders)</h3>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/salesOrder/create" class="btn btn-primary fw-bold"><i class="fas fa-plus"></i> إنشاء أمر بيع</a>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?> mb-3"><i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 align-middle text-center table-hover">
            <thead class="bg-light">
                <tr>
                    <th>رقم الأمر</th>
                    <th>التاريخ</th>
                    <th>العميل</th>
                    <th class="text-left">الإجمالي (SAR)</th>
                    <th>الحالة</th>
                    <th>بواسطة</th>
                    <th class="d-print-none">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $o): ?>
                <tr>
                    <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($o->order_number); ?></td>
                    <td class="font-monospace text-muted"><?php echo $o->order_date; ?></td>
                    <td class="fw-bold"><i class="fas fa-user text-muted me-1"></i> <?php echo htmlspecialchars($o->customer_name); ?></td>
                    <td class="font-monospace fw-black text-primary fs-5 text-left" style="direction:ltr; text-align: left;"><?php echo number_format($o->total_amount, 2); ?></td>
                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($o->status); ?></span></td>
                    <td><small class="text-muted"><?php echo htmlspecialchars($o->user_name); ?></small></td>
                    <td class="d-print-none">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="<?php echo URLROOT; ?>/salesOrder/show/<?php echo $o->id; ?>" class="btn-icon view text-primary"><i class="fas fa-eye"></i></a>
                            <?php if(Session::hasRole('admin') || Session::hasRole('super_admin')): ?>
                            <form action="<?php echo URLROOT; ?>/salesOrder/delete/<?php echo $o->id; ?>" method="POST" onsubmit="return confirm('تأكيد حذف أمر البيع؟');">
                                <button type="submit" class="btn-icon delete text-danger" style="border:none; background:none;"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; if(empty($orders)): ?>
                    <tr><td colspan="7" class="text-center text-muted p-5"><i class="fas fa-shopping-cart fs-1 opacity-25 mb-3 d-block"></i>لا توجد أوامر بيع.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>