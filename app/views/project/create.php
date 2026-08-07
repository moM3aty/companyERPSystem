<?php
// app/views/project/create.php
$customers = $customers ?? ($data['customers'] ?? []);
$employees = $employees ?? ($data['employees'] ?? []);
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-diagram-project text-primary"></i> تسجيل مشروع جديد</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/project/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">اسم المشروع <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="مثال: تنفيذ البنية التحتية لفرع جدة">
                </div>
                
                <div class="form-group">
                    <label class="form-label">كود المشروع (Code) <span class="required">*</span></label>
                    <input type="text" name="code" class="form-control font-monospace" required style="direction:ltr; text-align:right;" placeholder="PRJ-2024-001">
                </div>

                <div class="form-group">
                    <label class="form-label">العميل المرتبط</label>
                    <select name="customer_id" class="form-control">
                        <option value="">-- مشروع داخلي (بدون عميل) --</option>
                        <?php foreach($customers as $c): ?>
                            <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ البدء</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ التسليم المتوقع</label>
                    <input type="date" name="end_date" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">الميزانية المخصصة (Budget)</label>
                    <input type="number" name="budget" step="0.01" min="0" class="form-control font-monospace text-success fw-bold" value="0.00" style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">مدير المشروع (PM)</label>
                    <select name="project_manager" class="form-control">
                        <option value="">-- غير معين --</option>
                        <?php foreach($employees as $e): ?>
                            <option value="<?php echo $e->id; ?>"><?php echo htmlspecialchars($e->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">وصف ونطاق المشروع</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ المشروع</button>
            <a href="<?php echo URLROOT; ?>/project/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>