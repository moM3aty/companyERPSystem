<?php
// app/views/training/edit.php
$training = $data['training'] ?? null;
$employees = $data['employees'] ?? [];
?>
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-light"><h3 class="card-title text-dark"><i class="fas fa-pen text-accent"></i> تعديل بيانات التدريب</h3></div>
    <form action="<?php echo URLROOT; ?>/training/edit/<?php echo $training->id; ?>" method="POST">
        <div class="card-body form-grid">
            <div class="form-group full-width">
                <label class="form-label">الموظف <span class="required">*</span></label>
                <select name="employee_id" class="form-control" required>
                    <?php foreach($employees as $emp): ?><option value="<?php echo $emp->id; ?>" <?php echo $emp->id == $training->employee_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($emp->name); ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label class="form-label">اسم الدورة</label><input type="text" name="course_name" class="form-control" value="<?php echo htmlspecialchars($training->course_name); ?>" required></div>
            <div class="form-group"><label class="form-label">جهة التدريب</label><input type="text" name="provider" class="form-control" value="<?php echo htmlspecialchars($training->provider ?? ''); ?>"></div>
            <div class="form-group"><label class="form-label">تاريخ الانعقاد</label><input type="date" name="course_date" class="form-control" value="<?php echo $training->course_date; ?>" required></div>
            <div class="form-group"><label class="form-label">تاريخ الانتهاء</label><input type="date" name="expiry_date" class="form-control" value="<?php echo $training->expiry_date ?? ''; ?>"></div>
            <div class="form-group"><label class="form-label text-danger">التكلفة (ر.س)</label><input type="number" step="0.01" name="cost" class="form-control font-monospace text-center text-danger" value="<?php echo $training->cost; ?>"></div>
            <div class="form-group">
                <label class="form-label">تقييم الموظف</label>
                <select name="evaluation" class="form-control fw-bold">
                    <option value="pending" <?php echo $training->evaluation == 'pending' ? 'selected' : ''; ?>>لم يتم التقييم</option>
                    <option value="excellent" <?php echo $training->evaluation == 'excellent' ? 'selected' : ''; ?>>ممتاز</option>
                    <option value="good" <?php echo $training->evaluation == 'good' ? 'selected' : ''; ?>>جيد</option>
                    <option value="poor" <?php echo $training->evaluation == 'poor' ? 'selected' : ''; ?>>ضعيف</option>
                </select>
            </div>
            <div class="form-group full-width"><label class="form-label">المهارات المكتسبة</label><textarea name="skills_acquired" class="form-control" rows="3"><?php echo htmlspecialchars($training->skills_acquired ?? ''); ?></textarea></div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light"><button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button></div>
    </form>
</div>