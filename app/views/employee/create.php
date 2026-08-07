<?php
// app/views/employee/create.php
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-plus text-primary"></i> تسجيل موظف جديد</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/employee/create" method="POST">
        <div class="card-body border-bottom">
            <h4 class="mb-3" style="font-size:14px; font-weight:700; color:var(--text-dark); border-bottom:1px dashed var(--border); padding-bottom:8px;"><i class="fas fa-id-card text-muted"></i> البيانات الأساسية</h4>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">الاسم الرباعي <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="مثال: أحمد محمد عبدالله">
                </div>
                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control font-monospace" style="direction:ltr; text-align:right;" placeholder="emp@company.com">
                </div>
                <div class="form-group">
                    <label class="form-label">رقم الجوال</label>
                    <input type="text" name="phone" class="form-control font-monospace" style="direction:ltr; text-align:right;" placeholder="05XXXXXXXX">
                </div>
            </div>
        </div>

        <div class="card-body">
            <h4 class="mb-3" style="font-size:14px; font-weight:700; color:var(--text-dark); border-bottom:1px dashed var(--border); padding-bottom:8px;"><i class="fas fa-briefcase text-muted"></i> البيانات الوظيفية</h4>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">المسمى الوظيفي <span class="required">*</span></label>
                    <input type="text" name="position" class="form-control" required placeholder="مثال: محاسب، مندوب مبيعات...">
                </div>
                <div class="form-group">
                    <label class="form-label">الراتب الأساسي (ر.س) <span class="required">*</span></label>
                    <input type="number" name="salary" step="0.01" min="0" value="0.00" class="form-control font-monospace text-success fw-bold" required style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ التعيين <span class="required">*</span></label>
                    <input type="date" name="hire_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ الموظف</button>
            <a href="<?php echo URLROOT; ?>/employee/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>