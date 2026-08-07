<?php
// app/views/sanctions/edit.php
$sanction = $sanction ?? ($data['sanction'] ?? null);
$employees = $employees ?? ($data['employees'] ?? []);
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen text-accent"></i> تعديل بيانات الجزاء</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/sanction/edit/<?php echo $sanction->id; ?>" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">الموظف المخالف <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <?php foreach($employees as $emp): ?>
                            <option value="<?php echo $emp->id; ?>" <?php echo $sanction->employee_id == $emp->id ? 'selected' : ''; ?>><?php echo htmlspecialchars($emp->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">نوع الإجراء (الجزاء) <span class="required">*</span></label>
                    <select name="type" id="typeSelect" class="form-control" required>
                        <option value="warning" <?php echo $sanction->type == 'warning' ? 'selected' : ''; ?>>لفت نظر / إنذار (بدون خصم)</option>
                        <option value="deduction" <?php echo $sanction->type == 'deduction' ? 'selected' : ''; ?>>خصم مالي مباشر من الراتب</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">المبلغ المخصوم (ر.س)</label>
                    <input type="number" name="amount" id="amountInput" step="0.01" min="0" class="form-control font-monospace text-danger fw-bold" value="<?php echo $sanction->amount; ?>" <?php echo $sanction->type == 'warning' ? 'disabled' : 'required'; ?> style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ المخالفة والقرار <span class="required">*</span></label>
                    <input type="date" name="date" class="form-control" value="<?php echo $sanction->date; ?>" required>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">سبب المخالفة والمبررات <span class="required">*</span></label>
                    <textarea name="reason" class="form-control" rows="4" required><?php echo htmlspecialchars($sanction->reason); ?></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/sanction/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    const typeSelect = document.getElementById('typeSelect');
    const amountInput = document.getElementById('amountInput');

    typeSelect.addEventListener('change', function() {
        if(this.value === 'deduction') {
            amountInput.disabled = false;
            amountInput.required = true;
        } else {
            amountInput.disabled = true;
            amountInput.required = false;
            amountInput.value = '';
        }
    });
</script>