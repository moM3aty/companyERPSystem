<?php
// app/views/employeeContract/index.php
$contracts =$data['contracts'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-signature text-primary"></i> عقود الموظفين</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة الرواتب الأساسية، البدلات، وتواريخ صلاحية العقود الخاصة بالموظفين.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/employeeContract/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إضافة عقد جديد
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
                        <th>الموظف</th>
                        <th>تاريخ البداية</th>
                        <th>تاريخ الانتهاء</th>
                        <th class="text-left">الراتب الأساسي</th>
                        <th class="text-left">البدلات</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contracts as$c) : 
                        // 🟢 التعديل الجذري: استخدام المصفوفات لضمان التوافق مع PHP 7.4 و PHP 8 🟢
                        $statusClasses = [                             'active' => 'badge-success',                             'expired' => 'badge-danger',                             'terminated' => 'badge-secondary'                         ];$statusLabels = [
                            'active' => 'ساري',
                            'expired' => 'منتهي',
                            'terminated' => 'مفسوخ'
                        ];

                        $statusClass = $statusClasses[$c->status] ?? 'badge-secondary';
                        $statusLabel =$statusLabels[$c->status] ?? $c->status;
                    ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-dark"><i class="fas fa-user-tie text-muted me-1"></i> <?php echo htmlspecialchars($c->employee_name); ?></div>
                            <div class="text-muted font-monospace" style="font-size:11px;"><?php echo htmlspecialchars($c->position ?? '—'); ?></div>
                        </td>
                        <td class="text-muted font-monospace fs-6">
                            <?php echo date('Y-m-d', strtotime($c->start_date)); ?>
                        </td>
                        <td class="font-monospace fs-6 <?php echo empty($c->end_date) ? 'text-muted' : 'text-danger fw-bold'; ?>">
                            <?php echo !empty($c->end_date) ? date('Y-m-d', strtotime($c->end_date)) : 'مفتوح (غير محدد)'; ?>
                        </td>
                        <td class="font-monospace fw-bold text-success text-left" style="direction:ltr;">
                            <?php echo number_format($c->basic_salary, 2); ?>
                        </td>
                        <td class="font-monospace fw-bold text-info text-left" style="direction:ltr;">
                            <?php echo number_format($c->allowances, 2); ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <?php if(Session::hasRole('admin') || Session::hasRole('super_admin')): ?>
                                <form action="<?php echo URLROOT; ?>/employeeContract/delete/<?php echo $c->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف العقد نهائياً؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف العقد"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($contracts)) : ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted p-5">
                            <i class="fas fa-file-signature fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد عقود مسجلة للموظفين.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>