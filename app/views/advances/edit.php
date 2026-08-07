<?php
// app/views/advances/edit.php
$advance = $advance ?? ($data['advance'] ?? null);
$employees = $employees ?? ($data['employees'] ?? []);
?>
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen text-accent"></i> تعديل طلب السلفة</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/advance/edit/<?php echo $advance->id; ?>" method="POST">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">الموظف طالب السلفة <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <?php foreach ($employees as $emp) : ?>
                            <option value="<?php echo $emp->id; ?>" <?php echo $advance->employee_id == $emp->id ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($emp->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">مبلغ السلفة المطلوب (ر.س) <span class="required">*</span></label>
                    <input type="number" name="amount" class="form-control font-monospace text-danger fw-bold" step="0.01" min="1" value="<?php echo $advance->amount; ?>" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ تقديم الطلب <span class="required">*</span></label>
                    <input type="date" name="date" class="form-control" value="<?php echo $advance->date; ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">شهر الخصم (الاستقطاع) <span class="required">*</span></label>
                    <select name="deduction_month" class="form-control" required>
                        <?php 
                            $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
                            foreach($months as $i => $m) {
                                $val = $i + 1;
                                $sel = ($val == $advance->deduction_month) ? 'selected' : '';
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
                            for($y = $currentYear - 1; $y <= $currentYear + 2; $y++) {
                                $sel = ($y == $advance->deduction_year) ? 'selected' : '';
                                echo "<option value=\"$y\" $sel>$y</option>";
                            }
                        ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">سبب السلفة (اختياري)</label>
                    <input type="text" name="reason" class="form-control" value="<?php echo htmlspecialchars($advance->reason); ?>">
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/advance/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>