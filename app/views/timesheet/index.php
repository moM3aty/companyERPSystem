<?php
// app/views/timesheet/index.php
$timesheets = $data['timesheets'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-business-time text-primary"></i> السجل الشامل لأوقات العمل (Timesheets)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تتبع ومراقبة جميع أوقات إنجاز الموظفين المسجلة على كافة المشاريع والمهام.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/project/index" class="btn btn-primary">
        <i class="fas fa-diagram-project"></i> اختر مشروعاً لتسجيل وقت
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>التاريخ</th>
                        <th>الموظف المنفذ</th>
                        <th>المشروع المرتبط</th>
                        <th>المهمة</th>
                        <th>الفترة (من - إلى)</th>
                        <th class="text-center">إجمالي الساعات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($timesheets)): foreach($timesheets as $ts): ?>
                    <tr>
                        <td class="text-muted fs-6 font-monospace"><i class="far fa-calendar-alt text-success"></i> <?php echo date('Y-m-d', strtotime($ts->date)); ?></td>
                        <td class="fw-bold text-dark"><i class="fas fa-user-circle text-muted"></i> <?php echo htmlspecialchars($ts->employee_name); ?></td>
                        <td class="fw-bold text-primary"><?php echo htmlspecialchars($ts->project_name); ?></td>
                        <td class="text-muted fs-6"><?php echo htmlspecialchars($ts->task_title ?? 'عام على المشروع'); ?></td>
                        <td class="font-monospace text-muted" style="font-size: 13px;">
                            <?php echo date('H:i', strtotime($ts->start_time)); ?> - <?php echo date('H:i', strtotime($ts->end_time)); ?>
                        </td>
                        <td class="text-center font-monospace fw-bold text-info fs-5" style="direction:ltr;">
                            <?php echo number_format($ts->total_hours, 2); ?>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted p-5">
                            <i class="fas fa-stopwatch fa-2x mb-3 opacity-50 d-block"></i> 
                            لا توجد أوقات عمل مسجلة حالياً في النظام.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>