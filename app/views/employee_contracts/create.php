<?php
// المسار: app/views/employee_contracts/create.php
$employees = $data['employees'] ?? [];
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-contract text-primary"></i> توثيق عقد وظيفي جديد</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/employeeContract/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">الموظف (الطرف الثاني) <span class="required">*</span></label>
                    <select name="employee_id" id="empSelect" class="form-control" required>
                        <option value="">-- يرجى اختيار الموظف --</option>
                        <?php foreach ($employees as $emp) : ?>
                            <option value="<?php echo $emp->id; ?>" data-salary="<?php echo $emp->salary; ?>">
                                <?php echo htmlspecialchars($emp->name); ?> — <?php echo htmlspecialchars($emp->position ?? 'بدون مسمى'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="salaryHint" class="mt-2 text-muted" style="display:none; font-size:12px; background:var(--page-bg); padding:8px; border-radius:var(--radius-sm); border:1px dashed var(--border-color); align-items:center; justify-content:space-between;">
                        <span>الراتب المسجل في ملف الموظف: <strong id="salaryVal" class="font-monospace text-dark">0</strong> ر.س</span>
                        <button type="button" class="btn btn-secondary py-1 px-2" style="font-size:11px;" onclick="document.getElementById('contractValue').value = document.getElementById('empSelect').options[document.getElementById('empSelect').selectedIndex].dataset.salary;">استخدام هذا الراتب</button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">عنوان ونوع العقد <span class="required">*</span></label>
                    <input type="text" name="title" class="form-control" value="عقد عمل محدد المدة" required>
                </div>

                <div class="form-group">
                    <label class="form-label">رقم العقد المرجعي</label>
                    <input type="text" name="contract_number" class="form-control font-monospace" placeholder="يُترك فارغاً للتوليد التلقائي" style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ البداية <span class="required">*</span></label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ النهاية <span class="required">*</span></label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">قيمة العقد (الراتب) <span class="required">*</span></label>
                    <input type="number" name="value" id="contractValue" class="form-control font-monospace text-success fw-bold" step="0.01" min="0" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">حالة العقد</label>
                    <select name="status" class="form-control">
                        <option value="active">نشط (ساري المفعول)</option>
                        <option value="draft">مسودة (قيد الإعداد)</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> توثيق العقد</button>
            <a href="<?php echo URLROOT; ?>/employeeContract/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('empSelect').addEventListener('change', function() {
        const hint = document.getElementById('salaryHint');
        if(this.value) {
            document.getElementById('salaryVal').textContent = parseFloat(this.options[this.selectedIndex].dataset.salary).toLocaleString();
            hint.style.display = 'flex';
        } else {
            hint.style.display = 'none';
        }
    });
</script>