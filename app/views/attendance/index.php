<?php
// app/views/attendance/index.php
$attendanceList =$data['attendance'] ?? [];
$filterDate =$data['filter_date'] ?? date('Y-m-d');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-fingerprint text-primary"></i> الحضور والانصراف</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة سجلات الدوام اليومية للموظفين (حضور، غياب، وتأخير).</p>
    </div>
    <a href="<?php echo URLROOT; ?>/attendance/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> تسجيل حضور يدوي
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

<div class="card mb-4 bg-light d-print-none">
    <div class="card-body">
        <form action="<?php echo URLROOT; ?>/attendance/index" method="GET" class="d-flex gap-3 align-items-end flex-wrap">
            <div style="flex: 1; max-width: 300px;">
                <label class="form-label">عرض سجلات يوم:</label>
                <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($filterDate); ?>">
            </div>
            <div>
                <button type="submit" class="btn btn-primary" style="height: 46px;"><i class="fas fa-search"></i> عرض السجلات</button>
                <a href="<?php echo URLROOT; ?>/attendance/index" class="btn btn-secondary ms-2" style="height: 46px; line-height: 32px;"><i class="fas fa-list"></i> عرض الكل</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>الموظف</th>
                        <th>التاريخ</th>
                        <th class="text-center">وقت الحضور</th>
                        <th class="text-center">وقت الانصراف</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendanceList as$att) : 
                        $statusClasses = [                             'present' => 'badge-success',                             'absent' => 'badge-danger',                             'late' => 'badge-warning',                             'half_day' => 'badge-info',                             'leave' => 'badge-secondary'                         ];$statusLabels = [
                            'present' => 'حاضر',
                            'absent' => 'غائب',
                            'late' => 'متأخر',
                            'half_day' => 'نصف يوم',
                            'leave' => 'إجازة'
                        ];

                        $statusClass = $statusClasses[$att->status] ?? 'badge-secondary';
                        $statusLabel =$statusLabels[$att->status] ?? $att->status;
                    ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-dark"><i class="fas fa-user text-muted me-1"></i> <?php echo htmlspecialchars($att->employee_name); ?></div>
                            <div class="text-muted font-monospace" style="font-size:11px;"><?php echo htmlspecialchars($att->position ?? '—'); ?></div>
                        </td>
                        <td class="text-muted font-monospace fs-6">
                            <?php echo date('Y-m-d', strtotime($att->date)); ?>
                        </td>
                        <td class="text-center font-monospace fw-bold text-success" style="direction:ltr;">
                            <?php echo !empty($att->check_in) ? date('h:i A', strtotime($att->check_in)) : '—'; ?>
                        </td>
                        <td class="text-center font-monospace fw-bold text-danger" style="direction:ltr;">
                            <?php echo !empty($att->check_out) ? date('h:i A', strtotime($att->check_out)) : '—'; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                            <?php if(!empty($att->notes)): ?>
                                <i class="fas fa-comment-dots text-muted ms-1" title="<?php echo htmlspecialchars($att->notes); ?>" style="cursor:help;"></i>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/attendance/edit/<?php echo $att->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <?php if(Session::hasRole('admin') || Session::hasRole('super_admin')): ?>
                                <form action="<?php echo URLROOT; ?>/attendance/delete/<?php echo $att->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف السجل نهائياً؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($attendanceList)) : ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted p-5">
                            <i class="fas fa-fingerprint fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد سجلات حضور وانصراف في هذا اليوم.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>