<?php
// app/views/suppliers/index.php
$suppliers =$data['suppliers'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-truck text-primary"></i> دليل الموردين</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة بيانات الشركات والأفراد الذين يتم شراء البضائع والخدمات منهم.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/supplier/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إضافة مورد جديد
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
                        <th style="width: 25%;">اسم المورد</th>
                        <th>التواصل</th>
                        <th>الرقم الضريبي</th>
                        <th class="text-left">الرصيد الافتتاحي (ر.س)</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suppliers as$sup) : ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-dark"><i class="fas fa-building text-muted me-1"></i> <?php echo htmlspecialchars($sup->name); ?></div>
                            <div class="text-muted mt-1" style="font-size:11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                                <i class="fas fa-location-dot"></i> <?php echo htmlspecialchars($sup->address ?? 'بدون عنوان'); ?>
                            </div>
                        </td>
                        <td>
                            <div class="text-muted font-monospace" style="font-size: 12px; direction: ltr; text-align: right;"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($sup->phone ?? '—'); ?></div>
                            <div class="text-muted font-monospace mt-1" style="font-size: 11px; direction: ltr; text-align: right;"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($sup->email ?? '—'); ?></div>
                        </td>
                        <td>
                            <span class="badge badge-secondary font-monospace"><?php echo htmlspecialchars($sup->tax_number ?? 'غير مسجل'); ?></span>
                        </td>
                        <td class="font-monospace fw-bold text-primary text-left" style="direction:ltr;">
                            <?php echo number_format($sup->balance, 2); ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/supplier/edit/<?php echo $sup->id; ?>" class="btn-icon edit" title="تعديل البيانات"><i class="fas fa-pen"></i></a>
                                
                                <!-- 🟢 زر الحذف 🟢 -->
                                <?php if(Session::hasRole('admin') || Session::hasRole('super_admin') || Session::hasRole('manager')): ?>
                                <form action="<?php echo URLROOT; ?>/supplier/delete/<?php echo $sup->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد الحذف نهائياً؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف المورد"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($suppliers)) : ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted p-5">
                            <i class="fas fa-truck-ramp-box fs-1 mb-3 opacity-50 d-block"></i>
                            لا يوجد موردين مسجلين في النظام.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>