<?php
// app/views/training/create.php
$employees =$data['employees'] ?? [];
?>
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-light"><h3 class="card-title text-dark"><i class="fas fa-plus text-primary"></i> تسجيل دورة تدريبية</h3></div>
    <form action="<?php echo URLROOT; ?>/training/create" method="POST">
        <div class="card-body form-grid">
            <div class="form-group full-width">
                <label class="form-label">الموظف <span class="required">*</span></label>
                <select name="employee_id" class="form-control" required>
                    <option value="">-- اختر الموظف --</option>
                    <?php foreach($employees as $emp): ?><option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name); ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">اسم الدورة (Course Name) <span class="required">*</span></label>
                <input type="text" name="course_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">جهة التدريب (Provider)</label>
                <input type="text" name="provider" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">تاريخ الانعقاد <span class="required">*</span></label>
                <input type="date" name="course_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">تاريخ انتهاء الشهادة (إن وجد)</label>
                <input type="date" name="expiry_date" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label text-danger">تكلفة التدريب (Cost)</label>
                <input type="number" step="0.01" name="cost" class="form-control font-monospace fw-bold text-center text-danger" value="0.00" style="direction:ltr;">
            </div>
            <div class="form-group">
                <label class="form-label">تقييم الموظف للدورة</label>
                <select name="evaluation" class="form-control fw-bold">
                    <option value="pending">لم يتم التقييم</option>
                    <option value="excellent">ممتاز</option>
                    <option value="good">جيد</option>
                    <option value="poor">ضعيف</option>
                </select>
            </div>
            <div class="form-group full-width">
                <label class="form-label">المهارات المكتسبة (Skills Acquired)</label>
                <textarea name="skills_acquired" class="form-control" rows="3" placeholder="تفاصيل المهارات التي اكتسبها الموظف لضمها لمصفوفة المهارات..."></textarea>
            </div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ التدريب</button>
            <a href="<?php echo URLROOT; ?>/training/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>