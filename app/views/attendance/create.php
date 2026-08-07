<?php
// app/views/attendance/create.php
$employees = $employees ?? ($data['employees'] ?? []);
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-clock text-primary"></i> تسجيل حضور وانصراف</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/attendance/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">الموظف <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">-- اختر الموظف --</option>
                        <?php foreach ($employees as $emp) : ?>
                            <option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">التاريخ <span class="required">*</span></label>
                    <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">حالة الحضور <span class="required">*</span></label>
                    <select name="status" id="statusSelect" class="form-control" required>
                        <option value="present">حاضر (Present)</option>
                        <option value="late">متأخر (Late)</option>
                        <option value="absent">غائب (Absent)</option>
                        <option value="leave">مجاز (On Leave)</option>
                    </select>
                </div>

                <div class="form-group" id="checkInGroup">
                    <label class="form-label">وقت الحضور (Check In)</label>
                    <input type="time" name="check_in" id="checkIn" class="form-control font-monospace" value="08:00">
                </div>

                <div class="form-group" id="checkOutGroup">
                    <label class="form-label">وقت الانصراف (Check Out)</label>
                    <input type="time" name="check_out" id="checkOut" class="form-control font-monospace" value="16:00">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">ملاحظات والتبريرات</label>
                    <input type="text" name="notes" class="form-control" placeholder="تأخير بسبب زحمة سير، خروج مبكر...">
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ وتوثيق</button>
            <a href="<?php echo URLROOT; ?>/attendance/index" class="btn btn-secondary">إلغاء</a>
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
            checkIn.disabled = false; checkIn.value = '08:00';
            checkOut.disabled = false; checkOut.value = '16:00';
        }
    });
</script>