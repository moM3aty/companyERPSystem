<?php
// app/views/attendance/edit.php
$attendance =$data['attendance'] ?? null;
$employees =$data['employees'] ?? [];
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-pen text-accent"></i> تعديل سجل الحضور</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/attendance/edit/<?php echo $attendance->id; ?>" method="POST">
        <div class="card-body">
            <div class="form-grid" style="grid-template-columns: 1fr;">
                
                <div class="form-group">
                    <label class="form-label">الموظف <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">-- اختر الموظف --</option>
                        <?php foreach($employees as$emp): ?>
                            <option value="<?php echo $emp->id; ?>" <?php echo ($attendance->employee_id == $emp->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($emp->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">التاريخ <span class="required">*</span></label>
                    <input type="date" name="date" class="form-control" value="<?php echo $attendance->date; ?>" required>
                </div>

                <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group mb-0">
                        <label class="form-label text-success">وقت الحضور</label>
                        <input type="time" name="check_in" class="form-control font-monospace" value="<?php echo $attendance->check_in; ?>" style="direction:ltr;">
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label text-danger">وقت الانصراف</label>
                        <input type="time" name="check_out" class="form-control font-monospace" value="<?php echo $attendance->check_out; ?>" style="direction:ltr;">
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label class="form-label">حالة الدوام <span class="required">*</span></label>
                    <select name="status" class="form-control fw-bold" required>
                        <option value="present" <?php echo ($attendance->status == 'present') ? 'selected' : ''; ?>>حاضر</option>
                        <option value="absent" <?php echo ($attendance->status == 'absent') ? 'selected' : ''; ?>>غائب</option>
                        <option value="late" <?php echo ($attendance->status == 'late') ? 'selected' : ''; ?>>متأخر</option>
                        <option value="half_day" <?php echo ($attendance->status == 'half_day') ? 'selected' : ''; ?>>نصف يوم</option>
                        <option value="leave" <?php echo ($attendance->status == 'leave') ? 'selected' : ''; ?>>إجازة مسبقة</option>
                    </select>
                </div>

                <div class="form-group mt-3">
                    <label class="form-label">ملاحظات (سبب التأخير، إذن الخروج، إلخ)</label>
                    <input type="text" name="notes" class="form-control" value="<?php echo htmlspecialchars($attendance->notes ?? ''); ?>" placeholder="اختياري...">
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/attendance/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>