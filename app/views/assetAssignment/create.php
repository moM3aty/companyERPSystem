<?php
// app/views/assetAssignment/create.php
$employees = $data['employees'] ?? [];
?>
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-light"><h3 class="card-title text-dark"><i class="fas fa-laptop text-primary"></i> تسليم عهدة لموظف</h3></div>
    <form action="<?php echo URLROOT; ?>/assetAssignment/create" method="POST">
        <div class="card-body form-grid">
            <div class="form-group full-width">
                <label class="form-label">الموظف (Employee) <span class="required">*</span></label>
                <select name="employee_id" class="form-control" required>
                    <option value="">-- اختر --</option>
                    <?php foreach($employees as $emp): ?><option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name); ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">نوع العهدة (Asset Type) <span class="required">*</span></label>
                <select name="asset_type" class="form-control fw-bold" required>
                    <option value="Laptop">لابتوب (Laptop)</option>
                    <option value="Mobile Phone">هاتف جوال (Mobile Phone)</option>
                    <option value="SIM Card">شريحة اتصال (SIM Card)</option>
                    <option value="Vehicle">سيارة (Vehicle)</option>
                    <option value="Equipment">معدات (Equipment)</option>
                    <option value="Uniform">زي رسمي (Uniform)</option>
                    <option value="Keys">مفاتيح (Keys)</option>
                    <option value="Access Card">بطاقة دخول (Access Card)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">رقم العهدة / السيريال (Asset ID) <span class="required">*</span></label>
                <input type="text" name="asset_id" class="form-control font-monospace" required style="direction:ltr; text-align:right;">
            </div>
            <div class="form-group">
                <label class="form-label">تاريخ التسليم (Issue Date) <span class="required">*</span></label>
                <input type="date" name="issue_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">حالة العهدة عند التسليم (Condition)</label>
                <select name="condition_given" class="form-control">
                    <option value="New">جديدة (New)</option>
                    <option value="Good">جيدة (Good)</option>
                    <option value="Used">مستعملة (Used)</option>
                </select>
            </div>
            <div class="form-group full-width mt-2">
                <label class="form-label">ملاحظات (Notes)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="مواصفات الجهاز، المرفقات، شواحن..."></textarea>
            </div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> تسجيل العهدة</button></div>
    </form>
</div>