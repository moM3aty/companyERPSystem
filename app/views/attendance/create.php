<?php
// app/views/attendance/create.php
$employees = $data['employees'] ?? [];
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-plus text-primary"></i> تسجيل حضور/انصراف جديد</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/attendance/create" method="POST">
        <div class="card-body">
            <div class="form-grid" style="grid-template-columns: 1fr;">
                
                <div class="form-group">
                    <label class="form-label">الموظف <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">-- اختر الموظف --</option>
                        <?php foreach($employees as $emp): ?>
                            <option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">التاريخ <span class="required">*</span></label>
                    <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group mb-0">
                        <label class="form-label text-success">وقت الحضور</label>
                        <input type="time" name="check_in" class="form-control font-monospace" style="direction:ltr;">
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label text-danger">وقت الانصراف</label>
                        <input type="time" name="check_out" class="form-control font-monospace" style="direction:ltr;">
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label class="form-label">حالة الدوام <span class="required">*</span></label>
                    <select name="status" class="form-control fw-bold" required>
                        <option value="present" selected>حاضر</option>
                        <option value="absent">غائب</option>
                        <option value="late">متأخر</option>
                        <option value="half_day">نصف يوم</option>
                        <option value="leave">إجازة مسبقة</option>
                    </select>
                </div>

                <div class="form-group mt-3">
                    <label class="form-label">ملاحظات (سبب التأخير، إذن الخروج، إلخ)</label>
                    <input type="text" name="notes" class="form-control" placeholder="اختياري...">
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ السجل</button>
            <a href="<?php echo URLROOT; ?>/attendance/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>