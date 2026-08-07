<?php
// app/views/leave/create.php
$employees = $employees ?? ($data['employees'] ?? []);
$leaveTypes = $leaveTypes ?? ($data['leave_types'] ?? []);
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar-plus text-primary"></i> تقديم طلب إجازة</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/leave/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">الموظف صاحب الطلب <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">-- اختر الموظف --</option>
                        <?php foreach ($employees as $emp) : ?>
                            <option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">نوع الإجازة المطلوبة <span class="required">*</span></label>
                    <select name="leave_type_id" class="form-control" required>
                        <option value="">-- حدد النوع --</option>
                        <?php foreach ($leaveTypes as $type) : ?>
                            <option value="<?php echo $type->id; ?>"><?php echo htmlspecialchars($type->name); ?> (<?php echo $type->is_paid ? 'مدفوعة الأجر' : 'غير مدفوعة'; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تبدأ من تاريخ <span class="required">*</span></label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">تنتهي في تاريخ <span class="required">*</span></label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">مبررات وملاحظات الإجازة <span class="required">*</span></label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="يرجى كتابة سبب طلب الإجازة باختصار..." required></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> إرسال للإدارة</button>
            <a href="<?php echo URLROOT; ?>/leave/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>