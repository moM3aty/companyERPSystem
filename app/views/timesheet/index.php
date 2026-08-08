<?php
// app/views/timesheet/index.php
$timesheets = $data['timesheets'] ?? [];

// حساب إجمالي الساعات في كل السجلات
$totalOverallHours = 0;
foreach ($timesheets as $ts) {
    $totalOverallHours += (float)($ts->total_hours ?? 0);
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-stopwatch text-info"></i> السجل الشامل لتتبع الوقت</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">عرض جميع ساعات العمل المسجلة على كافة المشاريع والمهام.</p>
    </div>
    <div class="d-flex gap-3 align-items-center">
        <div class="bg-white p-2 px-3 rounded border shadow-sm text-center">
            <span class="text-muted fs-7 d-block fw-bold">إجمالي ساعات العمل</span>
            <span class="font-monospace fw-bold text-primary fs-5"><?php echo number_format($totalOverallHours, 2); ?> <small class="text-muted fs-7">ساعة</small></span>
        </div>
        <a href="<?php echo URLROOT; ?>/project/index" class="btn btn-primary">
            <i class="fas fa-plus"></i> تسجيل وقت جديد (اختر مشروع)
        </a>
    </div>
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
                        <th>التاريخ</th>
                        <th>المشروع</th>
                        <th>المهمة</th>
                        <th>الموظف</th>
                        <th class="text-center">الفترة</th>
                        <th class="text-center">إجمالي الساعات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($timesheets as $ts) : ?>
                    <tr>
                        <td class="text-muted fs-6 font-monospace"><i class="far fa-calendar-alt"></i> <?php echo $ts->date; ?></td>
                        <td>
                            <a href="<?php echo URLROOT; ?>/project/show/<?php echo $ts->project_id; ?>" class="fw-bold text-primary text-decoration-none">
                                <i class="fas fa-diagram-project text-muted me-1"></i> <?php echo htmlspecialchars($ts->project_name ?? 'غير معروف'); ?>
                            </a>
                        </td>
                        <td class="text-muted fs-6">
                            <?php echo htmlspecialchars($ts->task_title ?? 'وقت عام على المشروع'); ?>
                        </td>
                        <td class="fw-bold text-dark">
                            <i class="fas fa-user-circle text-muted"></i> <?php echo htmlspecialchars($ts->employee_name ?? 'غير محدد'); ?>
                        </td>
                        <td class="text-center font-monospace text-muted" style="font-size: 13px; direction:ltr;">
                            <?php echo date('H:i', strtotime($ts->start_time ?? '00:00')); ?> <i class="fas fa-arrow-right mx-1" style="font-size:9px;"></i> <?php echo date('H:i', strtotime($ts->end_time ?? '00:00')); ?>
                        </td>
                        <td class="text-center font-monospace fw-bold text-info fs-5" style="direction:ltr;">
                            <?php echo number_format((float)($ts->total_hours ?? 0), 2); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($timesheets)) : ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted p-5">
                            <i class="fas fa-stopwatch fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد أوقات عمل مسجلة في النظام بعد.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>