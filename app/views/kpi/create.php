<?php
// app/views/kpi/create.php
$employees = $data['employees'] ?? [];
?>
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-light"><h3 class="card-title text-dark"><i class="fas fa-plus text-primary"></i> تسجيل مؤشر أداء جديد (KPI)</h3></div>
    <form action="<?php echo URLROOT; ?>/kpi/create" method="POST">
        <div class="card-body form-grid">
            <div class="form-group full-width">
                <label class="form-label">الموظف (Employee) <span class="required">*</span></label>
                <select name="employee_id" class="form-control" required>
                    <option value="">-- اختر --</option>
                    <?php foreach($employees as $emp): ?><option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name); ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">اسم المؤشر (KPI Name) <span class="required">*</span></label>
                <input type="text" name="kpi_name" class="form-control" placeholder="مثال: Sales Target" required>
            </div>
            <div class="form-group">
                <label class="form-label">فترة التقييم (Review Period)</label>
                <select name="review_period" class="form-control fw-bold">
                    <option value="Quarterly (Q1)">ربع سنوي (Q1)</option>
                    <option value="Quarterly (Q2)">ربع سنوي (Q2)</option>
                    <option value="Quarterly (Q3)">ربع سنوي (Q3)</option>
                    <option value="Quarterly (Q4)">ربع سنوي (Q4)</option>
                    <option value="Mid-Year">نصف سنوي (Mid-Year)</option>
                    <option value="Annual">سنوي (Annual)</option>
                </select>
            </div>
            <div class="form-group border rounded p-2 bg-light">
                <label class="form-label text-primary">الهدف المطلوب (Target)</label>
                <input type="number" step="0.01" name="target_value" class="form-control font-monospace text-center fs-5 text-primary" required>
            </div>
            <div class="form-group border rounded p-2 bg-light">
                <label class="form-label text-success">النتيجة الفعلية (Actual)</label>
                <input type="number" step="0.01" name="actual_value" class="form-control font-monospace text-center fs-5 text-success" required>
                <small class="text-muted d-block mt-1 text-center">سيقوم النظام بحساب النسبة المئوية % آلياً.</small>
            </div>
            <div class="form-group">
                <label class="form-label text-danger">وزن المؤشر من التقييم العام (Weight %)</label>
                <input type="number" step="0.01" max="100" name="weight" class="form-control font-monospace text-danger fw-bold" value="25" style="direction:ltr; text-align:right;">
            </div>
            <div class="form-group">
                <label class="form-label">التقييم العام النهائي (Overall Rating)</label>
                <select name="overall_rating" class="form-control fw-bold">
                    <option value="Excellent">ممتاز (Excellent)</option>
                    <option value="Good" selected>جيد (Good)</option>
                    <option value="Needs Improvement">يحتاج تطوير (Needs Improvement)</option>
                    <option value="Poor">ضعيف (Poor)</option>
                </select>
            </div>
            <div class="form-group full-width mt-2">
                <label class="form-label">تقييم المدير (Manager Evaluation)</label>
                <textarea name="manager_evaluation" class="form-control" rows="2"></textarea>
            </div>
            <div class="form-group full-width mt-2">
                <label class="form-label">خطة التطوير (Development Plan)</label>
                <textarea name="development_plan" class="form-control" rows="2" placeholder="الدورات أو الخطوات المقترحة للتحسين..."></textarea>
            </div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ التقييم</button></div>
    </form>
</div>