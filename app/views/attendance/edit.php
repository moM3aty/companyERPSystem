<?php
// app/views/attendance/edit.php
$record = $record ?? ($data['record'] ?? null);
$employees = $employees ?? ($data['employees'] ?? []);
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen text-accent"></i> تعديل سجل حضور: <?php echo htmlspecialchars($record->employee_name); ?></h3>
    </div>

    <form action="<?php echo URLROOT; ?>/attendance/edit/<?php echo $record->id; ?>" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">الموظف <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <?php foreach ($employees as $emp) : ?>
                            <option value="<?php echo $emp->id; ?>" <?php echo $emp->id == $record->employee_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($emp->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">التاريخ <span class="required">*</span></label>
                    <input type="date" name="date" class="form-control" value="<?php echo $record->date; ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">حالة الحضور <span class="required">*</span></label>
                    <select name="status" id="statusSelect" class="form-control" required>
                        <option value="present" <?php echo $record->status == 'present' ? 'selected' : ''; ?>>حاضر (Present)</option>
                        <option value="late" <?php echo $record->status == 'late' ? 'selected' : ''; ?>>متأخر (Late)</option>
                        <option value="absent" <?php echo $record->status == 'absent' ? 'selected' : ''; ?>>غائب (Absent)</option>
                        <option value="leave" <?php echo $record->status == 'leave' ? 'selected' : ''; ?>>مجاز (On Leave)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">وقت الحضور (Check In)</label>
                    <input type="time" name="check_in" id="checkIn" class="form-control font-monospace" value="<?php echo $record->check_in ? date('H:i', strtotime($record->check_in)) : ''; ?>" <?php echo in_array($record->status, ['absent', 'leave']) ? 'disabled' : ''; ?>>
                </div>

                <div class="form-group">
                    <label class="form-label">وقت الانصراف (Check Out)</label>
                    <input type="time" name="check_out" id="checkOut" class="form-control font-monospace" value="<?php echo $record->check_out ? date('H:i', strtotime($record->check_out)) : ''; ?>" <?php echo in_array($record->status, ['absent', 'leave']) ? 'disabled' : ''; ?>>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">ملاحظات والتبريرات</label>
                    <input type="text" name="notes" class="form-control" value="<?php echo htmlspecialchars($record->notes); ?>">
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/attendance/index?date=<?php echo $record->date; ?>" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    const statusSelect = document.getElementById('statusSelect');
    const checkIn = document.getElementById('checkIn');
    const checkOut = document.getElementById('checkOut');

    statusSelect.addEventListener('change', function() {
        if(this.value === 'absent' || this.value === 'leave') {
            checkIn.value = ''; checkIn.disabled = true;
            checkOut.value = ''; checkOut.disabled = true;
        } else {
            checkIn.disabled = false;
            checkOut.disabled = false;
        }
    });
</script>