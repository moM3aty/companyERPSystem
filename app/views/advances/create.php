<?php
// app/views/advances/create.php
$employees = $employees ?? ($data['employees'] ?? []);
?>
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white mb-0"><i class="fas fa-hand-holding-dollar"></i> تقديم طلب سلفة</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/advance/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">الموظف طالب السلفة <span class="required">*</span></label>
                    <select name="employee_id" id="empSelect" class="form-control" required>
                        <option value="">-- اختر الموظف --</option>
                        <?php foreach ($employees as $emp) : ?>
                            <option value="<?php echo $emp->id; ?>" data-salary="<?php echo $emp->salary; ?>">
                                <?php echo htmlspecialchars($emp->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="salaryHint" class="mt-2 text-muted" style="display:none; font-size:12px; background:var(--page-bg); padding:8px; border-radius:var(--radius-sm); border:1px dashed var(--border);">
                        الراتب الأساسي للموظف: <strong id="salaryVal" class="font-monospace text-dark">0</strong> ر.س
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">مبلغ السلفة المطلوب (ر.س) <span class="required">*</span></label>
                    <input type="number" name="amount" id="amountInput" class="form-control font-monospace text-danger fw-bold" step="0.01" min="1" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ تقديم الطلب <span class="required">*</span></label>
                    <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">شهر الخصم (الاستقطاع) <span class="required">*</span></label>
                    <select name="deduction_month" class="form-control" required>
                        <?php 
                            $currentMonth = date('n');
                            $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
                            foreach($months as $i => $m) {
                                $val = $i + 1;
                                $sel = ($val == $currentMonth) ? 'selected' : '';
                                echo "<option value=\"$val\" $sel>$m ($val)</option>";
                            }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">سنة الخصم <span class="required">*</span></label>
                    <select name="deduction_year" class="form-control" required>
                        <?php 
                            $currentYear = date('Y');
                            for($y = $currentYear; $y <= $currentYear + 2; $y++) {
                                echo "<option value=\"$y\">$y</option>";
                            }
                        ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">سبب السلفة (اختياري)</label>
                    <input type="text" name="reason" class="form-control" placeholder="مثال: ظروف صحية، صيانة...">
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-warning"><i class="fas fa-paper-plane"></i> إرسال الطلب للاعتماد</button>
            <a href="<?php echo URLROOT; ?>/advance/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>
<script>
    const empSelect = document.getElementById('empSelect');
    const salaryHint = document.getElementById('salaryHint');
    const salaryVal = document.getElementById('salaryVal');

    empSelect.addEventListener('change', function() {
        const selectedOpt = this.options[this.selectedIndex];
        if(selectedOpt.value) {
            salaryVal.textContent = parseFloat(selectedOpt.dataset.salary).toLocaleString('ar-SA', {minimumFractionDigits: 2});
            salaryHint.style.display = 'block';
        } else {
            salaryHint.style.display = 'none';
        }
    });
</script>