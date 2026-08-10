<?php
// app/views/employeeContract/create.php
$employees = $data['employees'] ?? [];
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-plus text-primary"></i> تسجيل عقد وظيفي جديد</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/employeeContract/create" method="POST">
        <div class="card-body">
            <div class="alert alert-info mb-4" style="font-size: 13px;">
                <i class="fas fa-info-circle"></i> حفظ العقد سيؤدي تلقائياً إلى تحديث الراتب الأساسي للموظف في سجله الرئيسي إذا كانت حالة العقد "نشط".
            </div>

            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">الموظف <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">-- اختر الموظف --</option>
                        <?php foreach($employees as $emp): ?>
                            <?php $empName = $emp->name_ar ?: $emp->name; ?>
                            <option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($empName); ?> (<?php echo htmlspecialchars($emp->employee_number ?? '-'); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ بداية العقد <span class="required">*</span></label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label text-warning">تاريخ نهاية العقد (للتنبيهات)</label>
                    <input type="date" name="end_date" class="form-control">
                </div>

                <div class="form-group border border-primary p-2 rounded bg-light">
                    <label class="form-label text-primary">الراتب الأساسي (ر.س) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="basic_salary" class="form-control font-monospace fw-bold text-center text-primary fs-5" value="0.00" required style="direction:ltr;">
                </div>

                <div class="form-group border border-success p-2 rounded bg-light">
                    <label class="form-label text-success">إجمالي البدلات (ر.س)</label>
                    <input type="number" step="0.01" name="allowances" class="form-control font-monospace fw-bold text-center text-success fs-5" value="0.00" style="direction:ltr;">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">حالة العقد</label>
                    <select name="status" class="form-control fw-bold">
                        <option value="active" selected>ساري (Active)</option>
                        <option value="expired">منتهي (Expired)</option>
                        <option value="terminated">ملغي / مفسوخ (Terminated)</option>
                    </select>
                </div>

                <div class="form-group full-width mt-2">
                    <label class="form-label">ملاحظات / شروط خاصة</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="أية شروط أو ملاحظات تتعلق بالراتب أو مدة العقد..."></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ العقد</button>
            <a href="<?php echo URLROOT; ?>/employeeContract/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>