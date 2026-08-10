<?php
// app/views/sanction/create.php
$employees =$data['employees'] ?? [];
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-danger-light border-danger">
        <h3 class="card-title text-danger mb-0"><i class="fas fa-gavel"></i> إصدار قرار إداري (إنذار / خصم)</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/sanction/create" method="POST">
        <div class="card-body">
            <div class="alert alert-warning mb-4" style="font-size: 13px;">
                <i class="fas fa-exclamation-triangle"></i> التنبيه: الخصم المالي سيتم إدراجه آلياً وتخفيضه من راتب الموظف في مسير رواتب الشهر الذي صدر فيه القرار.
            </div>

            <div class="form-grid" style="grid-template-columns: 1fr;">
                
                <div class="form-group">
                    <label class="form-label">الموظف المخالف <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">-- اختر الموظف --</option>
                        <?php foreach($employees as$emp): ?>
                            <option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ حدوث المخالفة <span class="required">*</span></label>
                    <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">نوع الإجراء الإداري <span class="required">*</span></label>
                    <select name="type" id="sanctionType" class="form-control fw-bold" required onchange="toggleAmount()">
                        <option value="warning" selected>لفت نظر / إنذار كتابي (لا يوجد خصم مالي)</option>
                        <option value="deduction">خصم مالي من الراتب الأساسي</option>
                    </select>
                </div>

                <div class="form-group" id="amountGroup" style="display:none;">
                    <label class="form-label text-danger">قيمة الخصم (ر.س) <span class="required">*</span></label>
                    <input type="number" step="0.01" min="1" name="amount" class="form-control font-monospace fw-bold text-center text-danger fs-4" placeholder="0.00" style="direction:ltr;">
                </div>

                <div class="form-group mt-3">
                    <label class="form-label">سبب المخالفة والمبررات <span class="required">*</span></label>
                    <textarea name="reason" class="form-control" rows="4" placeholder="وصف دقيق للمخالفة ليتم إرفاقه في ملف الموظف..." required></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-danger"><i class="fas fa-check-circle"></i> اعتماد وتسجيل القرار</button>
            <a href="<?php echo URLROOT; ?>/sanction/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    function toggleAmount() {
        const type = document.getElementById('sanctionType').value;
        const amountGroup = document.getElementById('amountGroup');
        const amountInput = amountGroup.querySelector('input');
        
        if (type === 'deduction') {
            amountGroup.style.display = 'block';
            amountInput.setAttribute('required', 'required');
        } else {
            amountGroup.style.display = 'none';
            amountInput.removeAttribute('required');
            amountInput.value = '';
        }
    }
</script>