<?php
// app/views/exitProcess/create.php
$employees = $data['employees'] ?? [];
?>
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-danger-light border-danger"><h3 class="card-title text-danger mb-0"><i class="fas fa-user-minus"></i> بدء إجراءات إنهاء الخدمة</h3></div>
    <form action="<?php echo URLROOT; ?>/exitProcess/create" method="POST">
        <div class="card-body form-grid" style="grid-template-columns: 1fr;">
            <div class="form-group">
                <label class="form-label">الموظف المغادر <span class="required">*</span></label>
                <select name="employee_id" class="form-control" required>
                    <option value="">-- اختر --</option>
                    <?php foreach($employees as $emp): ?><option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->full_name); ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap:15px;">
                <div class="form-group mb-0"><label class="form-label">تاريخ طلب الاستقالة</label><input type="date" name="resignation_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required></div>
                <div class="form-group mb-0"><label class="form-label text-danger">تاريخ آخر يوم عمل</label><input type="date" name="last_working_day" class="form-control" required></div>
            </div>
            <div class="form-group mt-3"><label class="form-label">فترة الإنذار (بالأيام)</label><input type="number" name="notice_period" class="form-control font-monospace" value="30"></div>
            <div class="form-group"><label class="form-label">سبب المغادرة</label><textarea name="reason" class="form-control" rows="3" required></textarea></div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light"><button type="submit" class="btn btn-danger"><i class="fas fa-check"></i> حفظ وبدء الإخلاء</button></div>
    </form>
</div>