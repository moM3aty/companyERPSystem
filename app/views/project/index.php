<?php
// app/views/project/index.php
$projects = $data['projects'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-diagram-project text-primary"></i> إدارة المشاريع</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">متابعة سير العمل، الميزانيات، وتوزيع المهام على الفريق.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/project/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إضافة مشروع جديد
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
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>الكود</th>
                        <th style="width: 30%;">اسم المشروع</th>
                        <th>الفترة (من - إلى)</th>
                        <th>الميزانية</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $p) : 
                        $statusClass = match($p->status) {
                            'active' => 'badge-success',
                            'on_hold' => 'badge-warning',
                            'completed' => 'badge-info',
                            'cancelled' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($p->status) {
                            'active' => 'نشط',
                            'on_hold' => 'معلق',
                            'completed' => 'مكتمل',
                            'cancelled' => 'ملغي',
                            default => $p->status
                        };
                    ?>
                    <tr>
                        <td><span class="badge badge-secondary font-monospace"><?php echo htmlspecialchars($p->code ?? '—'); ?></span></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($p->name); ?></div>
                        </td>
                        <td class="text-muted font-monospace fs-6">
                            <?php echo $p->start_date ? date('Y-m-d', strtotime($p->start_date)) : '—'; ?> <i class="fas fa-arrow-left mx-1" style="font-size:10px;"></i> <?php echo $p->end_date ? date('Y-m-d', strtotime($p->end_date)) : '—'; ?>
                        </td>
                        <td class="font-monospace fw-bold text-success" style="direction:ltr; text-align:right;">
                            <?php echo number_format($p->budget, 2); ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/project/show/<?php echo $p->id; ?>" class="btn-icon view" title="إدارة المهام (Dashboard)"><i class="fas fa-chart-gantt"></i></a>
                                <a href="<?php echo URLROOT; ?>/timesheet/project/<?php echo $p->id; ?>" class="btn-icon view" title="سجل الوقت" style="border-color:var(--info); color:var(--info);"><i class="fas fa-stopwatch"></i></a>
                                <a href="<?php echo URLROOT; ?>/project/edit/<?php echo $p->id; ?>" class="btn-icon edit" title="تعديل الإعدادات"><i class="fas fa-pen"></i></a>
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/project/delete/<?php echo $p->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد الحذف؟ سيتم مسح المهام وسجلات الوقت المرتبطة.');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($projects)) : ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted p-5">
                            <i class="fas fa-diagram-project fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد مشاريع مسجلة بعد.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>