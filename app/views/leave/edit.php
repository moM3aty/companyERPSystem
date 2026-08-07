<?php
// app/views/leave/edit.php
$request = $request ?? ($data['request'] ?? null);
$employees = $employees ?? ($data['employees'] ?? []);
$leaveTypes = $leaveTypes ?? ($data['leave_types'] ?? []);
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen text-accent"></i> تعديل طلب الإجازة</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/leave/edit/<?php echo $request->id; ?>" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">الموظف صاحب الطلب <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <?php foreach ($employees as $emp) : ?>
                            <option value="<?php echo $emp->id; ?>" <?php echo $request->employee_id == $emp->id ? 'selected' : ''; ?>><?php echo htmlspecialchars($emp->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">نوع الإجازة المطلوبة <span class="required">*</span></label>
                    <select name="leave_type_id" class="form-control" required>
                        <?php foreach ($leaveTypes as $type) : ?>
                            <option value="<?php echo $type->id; ?>" <?php echo $request->leave_type_id == $type->id ? 'selected' : ''; ?>><?php echo htmlspecialchars($type->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تبدأ من تاريخ <span class="required">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo $request->start_date; ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">تنتهي في تاريخ <span class="required">*</span></label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo $request->end_date; ?>" required>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">مبررات وملاحظات الإجازة <span class="required">*</span></label>
                    <textarea name="reason" class="form-control" rows="3" required><?php echo htmlspecialchars($request->reason); ?></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/leave/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>