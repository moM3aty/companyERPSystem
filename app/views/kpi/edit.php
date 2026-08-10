<?php
// app/views/kpi/edit.php
$kpi = $data['kpi']?? null;
$employees = $data['employees']?? [];
?>
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning-light border-warning"><h3 class="card-title text-warning-dark mb-0"><i class="fas fa-pen"></i> تعديل بيانات مؤشر الأداء</h3></div>
    <form action="<?php echo URLROOT; ?>/kpi/edit/<?php echo $kpi->id; ?>" method="POST">
        <div class="card-body form-grid">
            <div class="form-group full-width">
                <label class="form-label">الموظف <span class="required">*</span></label>
                <select name="employee_id" class="form-control" required>
                    <?php foreach($employees as $emp): ?><option value="<?php echo $emp->id; ?>" <?php echo $emp->id == $kpi->employee_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($emp->name); ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label class="form-label">اسم المؤشر</label><input type="text" name="kpi_name" class="form-control" value="<?php echo htmlspecialchars($kpi->kpi_name); ?>" required></div>
            <div class="form-group"><label class="form-label">فترة التقييم</label><input type="text" name="review_period" class="form-control" value="<?php echo htmlspecialchars($kpi->review_period); ?>"></div>
            
            <div class="form-group border rounded p-2 bg-light"><label class="form-label text-primary">الهدف (Target)</label><input type="number" step="0.01" name="target_value" class="form-control font-monospace text-center fs-5 text-primary" value="<?php echo $kpi->target_value; ?>"></div>
            <div class="form-group border rounded p-2 bg-light"><label class="form-label text-success">النتيجة (Actual)</label><input type="number" step="0.01" name="actual_value" class="form-control font-monospace text-center fs-5 text-success" value="<?php echo $kpi->actual_value; ?>"></div>
            
            <div class="form-group"><label class="form-label text-danger">الوزن (Weight %)</label><input type="number" step="0.01" name="weight" class="form-control font-monospace text-danger" value="<?php echo $kpi->weight; ?>"></div>
            <div class="form-group"><label class="form-label">التقييم العام</label><input type="text" name="overall_rating" class="form-control fw-bold" value="<?php echo htmlspecialchars($kpi->overall_rating); ?>"></div>
            
            <div class="form-group full-width"><label class="form-label">تقييم المدير</label><textarea name="manager_evaluation" class="form-control" rows="2"><?php echo htmlspecialchars($kpi->manager_evaluation ?? ''); ?></textarea></div>
            <div class="form-group full-width"><label class="form-label">تعليقات الموظف</label><textarea name="employee_comments" class="form-control" rows="2"><?php echo htmlspecialchars($kpi->employee_comments ?? ''); ?></textarea></div>
            <div class="form-group full-width"><label class="form-label">خطة التطوير</label><textarea name="development_plan" class="form-control" rows="2"><?php echo htmlspecialchars($kpi->development_plan ?? ''); ?></textarea></div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light"><button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التحديثات</button></div>
    </form>
</div>