<?php
// app/views/supplier/index.php
$suppliers = $data['suppliers'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-truck text-primary"></i> إدارة الموردين (Suppliers)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة بيانات الموردين وأرصدتهم وحساباتهم.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/supplier/create" class="btn btn-primary fw-bold"><i class="fas fa-plus"></i> إضافة مورد جديد</a>
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
                    <th>اسم الشركة / المورد</th>
                    <th>الشخص المسؤول</th>
                    <th>رقم الهاتف</th>
                    <th class="text-left">الرصيد الحالي (SAR)</th>
                    <th class="d-print-none">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($suppliers as $s): 
                    $balance = $s->current_balance ?? ($s->balance ?? 0);
                    $balanceClass = $balance > 0 ? 'text-danger' : ($balance < 0 ? 'text-success' : 'text-dark');
                ?>
                <tr>
                    <td class="fw-bold text-dark"><i class="fas fa-building text-muted me-1"></i> <?php echo htmlspecialchars($s->company_name); ?></td>
                    <td><?php echo htmlspecialchars($s->contact_person ?: '—'); ?></td>
                    <td class="font-monospace"><?php echo htmlspecialchars($s->phone ?: '—'); ?></td>
                    <td class="font-monospace fw-black fs-5 text-left <?php echo $balanceClass; ?>" style="direction:ltr;">
                        <?php echo number_format($balance, 2); ?>
                    </td>
                    <td class="d-print-none">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="<?php echo URLROOT; ?>/supplier/edit/<?php echo $s->id; ?>" class="btn-icon view text-warning" title="تعديل"><i class="fas fa-edit"></i></a>
                            <?php if(Session::hasRole('admin') || Session::hasRole('super_admin')): ?>
                            <form action="<?php echo URLROOT; ?>/supplier/delete/<?php echo $s->id; ?>" method="POST" onsubmit="return confirm('تأكيد حذف المورد؟ لن يتم الحذف إذا كان مرتبطاً بفواتير.');">
                                <button type="submit" class="btn-icon delete text-danger" style="border:none; background:none;" title="حذف"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; if(empty($suppliers)): ?>
                    <tr><td colspan="5" class="text-center text-muted p-5"><i class="fas fa-truck fs-1 opacity-25 mb-3 d-block"></i>لا يوجد موردين مسجلين حالياً.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>