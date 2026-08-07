<?php
// app/views/sanctions/create.php
$employees = $employees ?? ($data['employees'] ?? []);
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-danger text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white mb-0"><i class="fas fa-gavel"></i> توقيع جزاء أو إنذار جديد</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/sanction/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">الموظف المخالف <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">-- اختر الموظف --</option>
                        <?php foreach($employees as $emp): ?>
                            <option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">نوع الإجراء (الجزاء) <span class="required">*</span></label>
                    <select name="type" id="typeSelect" class="form-control" required>
                        <option value="warning">لفت نظر / إنذار (بدون خصم)</option>
                        <option value="deduction">خصم مالي مباشر من الراتب</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">المبلغ المخصوم (ر.س)</label>
                    <input type="number" name="amount" id="amountInput" step="0.01" min="0" class="form-control font-monospace text-danger fw-bold" disabled style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ المخالفة والقرار <span class="required">*</span></label>
                    <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">سبب المخالفة والمبررات <span class="required">*</span></label>
                    <textarea name="reason" class="form-control" rows="4" placeholder="يرجى كتابة التفاصيل التي أدت لاتخاذ هذا الإجراء..." required></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-danger"><i class="fas fa-save"></i> حفظ وتوثيق الجزاء</button>
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
            amountInput.focus();
        } else {
            amountInput.disabled = true;
            amountInput.required = false;
            amountInput.value = '';
        }
    });
</script>