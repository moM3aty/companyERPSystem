<?php
// app/views/attendance/index.php
$records = $records ?? ($data['records'] ?? []);
$currentDate = $currentDate ?? ($data['current_date'] ?? date('Y-m-d'));
?>

<div class="d-flex justify-content-between align-items-center" style="margin-bottom: 24px;">
    <div>
        <h3 class="card-title mb-0"><i class="fas fa-fingerprint text-primary"></i> السجل اليومي للحضور</h3>
        <p class="text-muted mt-0">عرض حالات دوام الموظفين، أوقات الوصول والانصراف.</p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <form action="<?php echo URLROOT; ?>/attendance/index" method="GET" class="d-flex align-items-center gap-2">
            <input type="date" name="date" class="form-control font-monospace" value="<?php echo htmlspecialchars($currentDate); ?>" style="width: auto;">
            <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i> عرض</button>
        </form>
        <a href="<?php echo URLROOT; ?>/attendance/create" class="btn btn-primary">
            <i class="fas fa-user-clock"></i> تسجيل حضور جديد
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>الموظف</th>
                        <th>وقت الحضور (Check In)</th>
                        <th>وقت الانصراف (Check Out)</th>
                        <th class="text-center">الحالة</th>
                        <th>ملاحظات</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($records)): foreach ($records as $rec) : 
                        $statusClass = match($rec->status) {
                            'present' => 'badge-success',
                            'absent' => 'badge-danger',
                            'late' => 'badge-warning',
                            'leave' => 'badge-purple',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($rec->status) {
                            'present' => '<i class="fas fa-check"></i> حاضر',
                            'absent' => '<i class="fas fa-xmark"></i> غائب',
                            'late' => '<i class="fas fa-clock"></i> متأخر',
                            'leave' => '<i class="fas fa-calendar-minus"></i> مجاز',
                            default => $rec->status
                        };
                    ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($rec->employee_name); ?></div>
                            <div style="font-size:11px;" class="text-muted"><?php echo htmlspecialchars($rec->position ?? 'موظف'); ?></div>
                        </td>
                        <td>
                            <?php if ($rec->check_in): ?>
                                <span class="badge badge-secondary font-monospace"><i class="fas fa-arrow-right-to-bracket text-success"></i> <?php echo date('H:i', strtotime($rec->check_in)); ?></span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($rec->check_out): ?>
                                <span class="badge badge-secondary font-monospace"><i class="fas fa-arrow-right-from-bracket text-danger"></i> <?php echo date('H:i', strtotime($rec->check_out)); ?></span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                        </td>
                        <td class="text-muted" style="font-size: 13px;">
                            <?php echo htmlspecialchars($rec->notes ?? '—'); ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/attendance/edit/<?php echo $rec->id; ?>" class="btn-icon edit" title="تعديل السجل"><i class="fas fa-pen"></i></a>
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/attendance/delete/<?php echo $rec->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا السجل؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted" style="padding: 40px;">
                            <i class="fas fa-clipboard-user" style="font-size: 40px; opacity:0.3; margin-bottom:10px; display:block;"></i>
                            لا توجد سجلات حضور ليوم <?php echo htmlspecialchars($currentDate); ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>