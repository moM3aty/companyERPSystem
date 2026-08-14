<?php
// app/views/account/create.php
$accounts = $data['accounts'] ?? [];
?>
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-light"><h3 class="card-title text-dark"><i class="fas fa-plus text-primary"></i> إضافة حساب محاسبي</h3></div>
    <form action="<?php echo URLROOT; ?>/account/create" method="POST">
        <div class="card-body form-grid" style="grid-template-columns: 1fr;">
            <div class="form-group">
                <label class="form-label">رقم الحساب (Account Code) <span class="required">*</span></label>
                <input type="text" name="account_code" class="form-control font-monospace" required>
            </div>
            <div class="form-group">
                <label class="form-label">اسم الحساب (Account Name) <span class="required">*</span></label>
                <input type="text" name="account_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">نوع الحساب (Account Type) <span class="required">*</span></label>
                <select name="account_type" class="form-control fw-bold" required>
                    <option value="Asset">أصول (Asset)</option>
                    <option value="Liability">خصوم / التزامات (Liability)</option>
                    <option value="Equity">حقوق الملكية (Equity)</option>
                    <option value="Revenue">إيرادات (Revenue)</option>
                    <option value="Expense">مصروفات (Expense)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">الحساب الرئيسي (Parent Account)</label>
                <select name="parent_id" class="form-control">
                    <option value="">-- حساب رئيسي (بدون أب) --</option>
                    <?php foreach($accounts as $acc): ?>
                        <option value="<?php echo $acc->id; ?>"><?php echo htmlspecialchars($acc->account_code . ' - ' . $acc->account_name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">الوصف</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
            </div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light"><button type="submit" class="btn btn-primary">حفظ الحساب</button></div>
    </form>
</div>