<?php
// app/views/account/edit.php
$account = $data['account'] ?? null;
$accountsList = $data['accounts'] ?? [];
?>
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-warning-light border-warning">
        <h3 class="card-title text-warning-dark mb-0"><i class="fas fa-pen"></i> تعديل حساب محاسبي</h3>
    </div>
    <form action="<?php echo URLROOT; ?>/account/edit/<?php echo $account->id; ?>" method="POST">
        <div class="card-body form-grid" style="grid-template-columns: 1fr;">
            <div class="form-group">
                <label class="form-label">رقم الحساب (Account Code) <span class="required">*</span></label>
                <input type="text" name="account_code" class="form-control font-monospace" value="<?php echo htmlspecialchars($account->account_code); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">اسم الحساب (Account Name) <span class="required">*</span></label>
                <input type="text" name="account_name" class="form-control" value="<?php echo htmlspecialchars($account->account_name); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">نوع الحساب (Account Type) <span class="required">*</span></label>
                <select name="account_type" class="form-control fw-bold text-primary" required>
                    <option value="Asset" <?php echo $account->account_type == 'Asset' ? 'selected' : ''; ?>>أصول (Asset)</option>
                    <option value="Liability" <?php echo $account->account_type == 'Liability' ? 'selected' : ''; ?>>خصوم / التزامات (Liability)</option>
                    <option value="Equity" <?php echo $account->account_type == 'Equity' ? 'selected' : ''; ?>>حقوق الملكية (Equity)</option>
                    <option value="Revenue" <?php echo $account->account_type == 'Revenue' ? 'selected' : ''; ?>>إيرادات (Revenue)</option>
                    <option value="Expense" <?php echo $account->account_type == 'Expense' ? 'selected' : ''; ?>>مصروفات (Expense)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">الحساب الرئيسي (Parent Account)</label>
                <select name="parent_id" class="form-control">
                    <option value="">-- حساب رئيسي (بدون أب) --</option>
                    <?php foreach($accountsList as $acc): 
                        if($acc->id == $account->id) continue; // منع اختيار الحساب كأب لنفسه
                    ?>
                        <option value="<?php echo $acc->id; ?>" <?php echo $account->parent_id == $acc->id ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($acc->account_code . ' - ' . $acc->account_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">الوصف</label>
                <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($account->description ?? ''); ?></textarea>
            </div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/account/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>