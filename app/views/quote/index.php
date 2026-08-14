<?php
// app/views/quote/index.php
$quotes = $data['quotes'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-signature text-primary"></i> عروض الأسعار (Quotations)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة عروض الأسعار الموجهة للعملاء (الفعليين والمحتملين).</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/quote/create" class="btn btn-primary fw-bold"><i class="fas fa-plus"></i> إنشاء عرض سعر جديد</a>
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
                    <th>رقم العرض</th>
                    <th>التاريخ</th>
                    <th>العميل المستهدف</th>
                    <th class="text-left">المبلغ الإجمالي (SAR)</th>
                    <th>الحالة</th>
                    <th>المُنشئ</th>
                    <th class="d-print-none">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($quotes as $q): 
                    $badgeClass = 'bg-secondary';
                    if ($q->status == 'Sent') $badgeClass = 'bg-info';
                    elseif ($q->status == 'Accepted') $badgeClass = 'bg-success';
                    elseif ($q->status == 'Rejected') $badgeClass = 'bg-danger';

                    $targetName = $q->customer_name ?: $q->lead_name;
                    $targetIcon = $q->customer_name ? '<i class="fas fa-user-check text-success" title="عميل حالي"></i>' : '<i class="fas fa-user-clock text-warning" title="عميل محتمل"></i>';
                ?>
                <tr>
                    <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($q->quote_number); ?></td>
                    <td class="font-monospace text-muted"><?php echo $q->quote_date; ?></td>
                    <td class="fw-bold"><?php echo $targetIcon; ?> <?php echo htmlspecialchars($targetName ?? 'غير محدد'); ?></td>
                    <td class="font-monospace fw-black text-primary fs-5 text-left" style="direction:ltr; text-align: left;">
                        <?php echo number_format($q->total_amount, 2); ?>
                    </td>
                    <td><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($q->status); ?></span></td>
                    <td><small class="text-muted"><?php echo htmlspecialchars($q->user_name); ?></small></td>
                    <td class="d-print-none">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="<?php echo URLROOT; ?>/quote/show/<?php echo $q->id; ?>" class="btn-icon view text-primary" title="عرض للطباعة"><i class="fas fa-eye"></i></a>
                            
                            <?php if(Session::hasRole('admin') || Session::hasRole('super_admin') || Session::hasRole('manager')): ?>
                            <form action="<?php echo URLROOT; ?>/quote/delete/<?php echo $q->id; ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف عرض السعر نهائياً؟');">
                                <button type="submit" class="btn-icon delete text-danger" style="border:none; background:none;" title="حذف العرض"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; if(empty($quotes)): ?>
                    <tr><td colspan="7" class="text-center text-muted p-5"><i class="fas fa-file-signature fs-1 opacity-25 mb-3 d-block"></i>لا توجد عروض أسعار مسجلة حتى الآن.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>