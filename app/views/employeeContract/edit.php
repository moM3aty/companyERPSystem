<?php
// app/views/employeeContract/edit.php
$contract = $data['contract'] ?? null;
$employees = $data['employees'] ?? [];
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-pen text-accent"></i> تعديل بيانات العقد</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/employeeContract/edit/<?php echo $contract->id; ?>" method="POST">
        <div class="card-body">

            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">الموظف <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <?php foreach($employees as $emp): ?>
                            <?php $empName = $emp->name_ar ?: $emp->name; ?>
                            <option value="<?php echo $emp->id; ?>" <?php echo $emp->id == $contract->employee_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($empName); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ بداية العقد <span class="required">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo $contract->start_date; ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label text-warning">تاريخ نهاية العقد (للتنبيهات)</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo $contract->end_date ?? ''; ?>">
                </div>

                <div class="form-group border border-primary p-2 rounded bg-light">
                    <label class="form-label text-primary">الراتب الأساسي (ر.س) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="basic_salary" class="form-control font-monospace fw-bold text-center text-primary fs-5" value="<?php echo $contract->basic_salary; ?>" required style="direction:ltr;">
                </div>

                <div class="form-group border border-success p-2 rounded bg-light">
                    <label class="form-label text-success">إجمالي البدلات (ر.س)</label>
                    <input type="number" step="0.01" name="allowances" class="form-control font-monospace fw-bold text-center text-success fs-5" value="<?php echo $contract->allowances; ?>" style="direction:ltr;">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">حالة العقد</label>
                    <select name="status" class="form-control fw-bold">
                        <option value="active" <?php echo $contract->status == 'active' ? 'selected' : ''; ?>>ساري (Active)</option>
                        <option value="expired" <?php echo $contract->status == 'expired' ? 'selected' : ''; ?>>منتهي (Expired)</option>
                        <option value="terminated" <?php echo $contract->status == 'terminated' ? 'selected' : ''; ?>>ملغي / مفسوخ (Terminated)</option>
                    </select>
                </div>

                <div class="form-group full-width mt-2">
                    <label class="form-label">ملاحظات / شروط خاصة</label>
                    <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($contract->notes ?? ''); ?></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/employeeContract/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>