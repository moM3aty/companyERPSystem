<?php
// المسار: app/views/project/timesheets.php
$project = $project ?? ($data['project'] ?? null);
$timesheets = $timesheets ?? ($data['timesheets'] ?? []);
$tasks = $tasks ?? ($data['tasks'] ?? []);
$employees = $employees ?? ($data['employees'] ?? []);
$totalHours = $totalHours ?? ($data['totalHours'] ?? 0);

// حماية في حال لم يتم تمرير المشروع
if (!$project) {
    echo "<div class='alert alert-danger'>خطأ: بيانات المشروع غير متوفرة.</div>";
    return;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-stopwatch text-info"></i> سجل تتبع الوقت (Timesheets)</h3>
        <p class="text-muted mt-1 font-monospace fs-6">مشروع: <?php echo htmlspecialchars($project->name ?? ''); ?></p>
    </div>
    <div class="text-left bg-white p-3 rounded border shadow-sm">
        <div class="text-muted fs-6 fw-bold text-uppercase mb-1">إجمالي الساعات المسجلة</div>
        <div class="font-monospace fw-bold text-primary" style="font-size: 24px; direction:ltr; text-align:right;">
            <?php echo number_format((float)$totalHours, 2); ?> <span class="fs-6 text-muted">Hrs</span>
        </div>
    </div>
</div>

<div class="form-grid" style="grid-template-columns: 1fr 2fr; align-items: start;">
    
    <!-- نموذج تسجيل الوقت -->
    <div class="card mb-0">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-clock text-primary"></i> تسجيل وقت إنجاز</h3>
        </div>
        <form action="<?php echo URLROOT; ?>/timesheet/logTime/<?php echo $project->id; ?>" method="POST">
            <div class="card-body form-group gap-3">
                <div class="form-group">
                    <label class="form-label">الموظف المنفذ <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">-- اختر الموظف --</option>
                        <?php foreach($employees as $emp): ?>
                            <option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">مرتبط بمهمة (اختياري)</label>
                    <select name="task_id" class="form-control">
                        <option value="">-- عام على المشروع --</option>
                        <?php foreach($tasks as $t): ?>
                            <option value="<?php echo $t->id; ?>"><?php echo htmlspecialchars($t->title); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الإنجاز <span class="required">*</span></label>
                    <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">من الساعة <span class="required">*</span></label>
                        <input type="time" name="start_time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">إلى الساعة <span class="required">*</span></label>
                        <input type="time" name="end_time" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">ملاحظات العمل</label>
                    <textarea name="note" class="form-control" rows="2" placeholder="ما الذي تم إنجازه في هذا الوقت..."></textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> حفظ الوقت</button>
            </div>
        </form>
    </div>

    <!-- جدول السجلات -->
    <div class="card mb-0 h-100">
        <div class="card-header d-flex justify-content-between">
            <h3 class="card-title"><i class="fas fa-list text-success"></i> الحركات المسجلة للمشروع</h3>
            <a href="<?php echo URLROOT; ?>/project/show/<?php echo $project->id; ?>" class="btn btn-sm btn-secondary">العودة للمشروع</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>التاريخ</th>
                            <th>الموظف</th>
                            <th>المهمة</th>
                            <th>الفترة</th>
                            <th class="text-center">إجمالي الساعات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($timesheets as $ts): ?>
                        <tr>
                            <td class="text-muted fs-6 font-monospace"><?php echo $ts->date; ?></td>
                            <td class="fw-bold text-dark"><i class="fas fa-user-circle text-muted"></i> <?php echo htmlspecialchars($ts->employee_name ?? ''); ?></td>
                            <td class="text-muted fs-6"><?php echo htmlspecialchars($ts->task_title ?? 'عام'); ?></td>
                            <td class="font-monospace text-muted" style="font-size: 12px; direction:ltr; text-align:right;">
                                <?php echo date('H:i', strtotime($ts->start_time ?? '00:00')); ?> - <?php echo date('H:i', strtotime($ts->end_time ?? '00:00')); ?>
                            </td>
                            <td class="text-center font-monospace fw-bold text-info fs-5" style="direction:ltr;">
                                <?php echo number_format((float)($ts->total_hours ?? 0), 2); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($timesheets)): ?>
                        <tr><td colspan="5" class="text-center text-muted p-5">لا توجد أوقات مسجلة على هذا المشروع.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>