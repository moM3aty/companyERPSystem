<?php
// app/views/employee/edit.php
$emp = $emp ?? ($data['employee'] ?? null);
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen text-accent"></i> تعديل بيانات الموظف: <?php echo htmlspecialchars($emp->name); ?></h3>
    </div>

    <form action="<?php echo URLROOT; ?>/employee/edit/<?php echo $emp->id; ?>" method="POST">
        <div class="card-body border-bottom">
            <h4 class="mb-3" style="font-size:14px; font-weight:700; color:var(--text-dark); border-bottom:1px dashed var(--border); padding-bottom:8px;"><i class="fas fa-id-card text-muted"></i> البيانات الأساسية</h4>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">الاسم الرباعي <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($emp->name); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control font-monospace" value="<?php echo htmlspecialchars($emp->email); ?>" style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">رقم الجوال</label>
                    <input type="text" name="phone" class="form-control font-monospace" value="<?php echo htmlspecialchars($emp->phone); ?>" style="direction:ltr; text-align:right;">
                </div>
            </div>
        </div>

        <div class="card-body">
            <h4 class="mb-3" style="font-size:14px; font-weight:700; color:var(--text-dark); border-bottom:1px dashed var(--border); padding-bottom:8px;"><i class="fas fa-briefcase text-muted"></i> البيانات الوظيفية</h4>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">المسمى الوظيفي <span class="required">*</span></label>
                    <input type="text" name="position" class="form-control" value="<?php echo htmlspecialchars($emp->position); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">الراتب الأساسي (ر.س) <span class="required">*</span></label>
                    <input type="number" name="salary" step="0.01" min="0" class="form-control font-monospace text-success fw-bold" value="<?php echo $emp->salary; ?>" required style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ التعيين <span class="required">*</span></label>
                    <input type="date" name="hire_date" class="form-control" value="<?php echo date('Y-m-d', strtotime($emp->hire_date)); ?>" required>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/employee/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>