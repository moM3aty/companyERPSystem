<?php
// app/views/employeeContract/create.php
$employees =$data['employees'] ?? [];
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-plus text-primary"></i> تسجيل عقد موظف جديد</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/employeeContract/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">اختر الموظف <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">-- اختر من قائمة الموظفين --</option>
                        <?php foreach($employees as$emp): ?>
                            <option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name . ' - ' . ($emp->position ?? '')); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ بداية العقد <span class="required">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ نهاية العقد (اختياري)</label>
                    <input type="date" name="end_date" class="form-control">
                    <small class="text-muted">اتركه فارغاً إذا كان العقد غير محدد المدة.</small>
                </div>

                <div class="form-group border rounded p-3 bg-light">
                    <label class="form-label text-success">الراتب الأساسي (ر.س) <span class="required">*</span></label>
                    <input type="number" name="basic_salary" step="0.01" class="form-control font-monospace fw-bold text-success text-center" required style="font-size: 18px;">
                </div>

                <div class="form-group border rounded p-3">
                    <label class="form-label text-info">إجمالي البدلات (ر.س)</label>
                    <input type="number" name="allowances" step="0.01" class="form-control font-monospace fw-bold text-info text-center" value="0.00" style="font-size: 18px;">
                    <small class="text-muted d-block text-center mt-1">بدل السكن، المواصلات، إلخ...</small>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">حالة العقد</label>
                    <select name="status" class="form-control fw-bold">
                        <option value="active" selected>ساري (Active)</option>
                        <option value="expired">منتهي (Expired)</option>
                        <option value="terminated">مفسوخ (Terminated)</option>
                    </select>
                </div>

                <div class="form-group full-width mt-2">
                    <label class="form-label">ملاحظات / شروط خاصة</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="أية شروط تخص ساعات العمل أو الإجازات..."></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ وتوثيق العقد</button>
            <a href="<?php echo URLROOT; ?>/employeeContract/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>