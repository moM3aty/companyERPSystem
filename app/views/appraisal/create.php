<?php
// app/views/appraisal/create.php
$employees = $data['employees'] ?? [];
?>
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-star text-warning"></i> تقييم موظف (Evaluation)</h3>
    </div>
    <form action="<?php echo URLROOT; ?>/appraisal/create" method="POST">
        <div class="card-body">
            
            <div class="alert alert-info mb-4" style="font-size: 13px;">
                <i class="fas fa-info-circle"></i> سيقوم النظام تلقائياً بحساب النتيجة النهائية وتحديد التقدير (ممتاز، جيد جداً...) بناءً على المتوسط الحسابي.
            </div>

            <div class="form-grid" style="grid-template-columns: 1fr;">
                <div class="form-group">
                    <label class="form-label">الموظف <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">-- اختر الموظف --</option>
                        <?php foreach($employees as $emp): ?>
                            <?php $empName = $emp->name_ar ?: $emp->name; ?>
                            <option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($empName); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">تاريخ التقييم <span class="required">*</span></label>
                    <input type="date" name="evaluation_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="p-3 border rounded bg-light mt-2 form-grid" style="grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                    <div class="form-group mb-0">
                        <label class="form-label text-primary" style="font-size:11px;">المهام والإنجاز (KPI) %</label>
                        <input type="number" name="performance_score" min="0" max="100" class="form-control font-monospace fw-bold text-center text-primary" value="100" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label text-info" style="font-size:11px;">السلوك والانضباط %</label>
                        <input type="number" name="behavior_score" min="0" max="100" class="form-control font-monospace fw-bold text-center text-info" value="100" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label text-success" style="font-size:11px;">الحضور والالتزام %</label>
                        <input type="number" name="attendance_score" min="0" max="100" class="form-control font-monospace fw-bold text-center text-success" value="100" required>
                    </div>
                </div>
                
                <div class="form-group mt-4">
                    <label class="form-label">ملاحظات المدير وتوصيات التطوير</label>
                    <textarea name="comments" class="form-control" rows="3" placeholder="نقاط القوة، نقاط تحتاج لتطوير..."></textarea>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-warning"><i class="fas fa-check-double"></i> اعتماد وحفظ التقييم</button>
            <a href="<?php echo URLROOT; ?>/appraisal/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>