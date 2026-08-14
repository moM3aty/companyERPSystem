<?php
// app/views/treasury/edit.php
$treasury = $data['treasury'] ?? null;
$accounts = $data['accounts'] ?? [];
?>
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-warning-light border-warning">
        <h3 class="card-title text-warning-dark mb-0"><i class="fas fa-pen"></i> تعديل بيانات الصندوق / البنك</h3>
    </div>
    <form action="<?php echo URLROOT; ?>/treasury/edit/<?php echo $treasury->id; ?>" method="POST">
        <div class="card-body form-grid" style="grid-template-columns: 1fr;">
            
            <div class="form-group">
                <label class="form-label">الاسم (مثال: بنك الراجحي، صندوق الفرع) <span class="required">*</span></label>
                <input type="text" name="name" class="form-control fw-bold" value="<?php echo htmlspecialchars($treasury->name); ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">النوع (Type)</label>
                <select name="type" class="form-control fw-bold">
                    <option value="Cash" <?php echo $treasury->type == 'Cash' ? 'selected' : ''; ?>>صندوق نقدي (Cash)</option>
                    <option value="Bank" <?php echo $treasury->type == 'Bank' ? 'selected' : ''; ?>>حساب بنكي (Bank Account)</option>
                    <option value="Petty Cash" <?php echo $treasury->type == 'Petty Cash' ? 'selected' : ''; ?>>عهدة نقدية (Petty Cash)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">رقم الحساب / الآيبان (للبنوك)</label>
                <input type="text" name="account_number" class="form-control font-monospace" style="direction:ltr; text-align:right;" value="<?php echo htmlspecialchars($treasury->account_number ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label text-muted">الرصيد الافتتاحي (لا يمكن تعديله من هنا)</label>
                <input type="text" class="form-control font-monospace fw-bold text-muted text-center bg-light" value="<?php echo number_format($treasury->opening_balance, 2); ?>" disabled style="direction:ltr;">
            </div>
            
            <div class="form-group">
                <label class="form-label">ربط بدليل الحسابات (شجرة الحسابات)</label>
                <select name="chart_account_id" class="form-control">
                    <option value="">-- اختر الحساب المحاسبي المرتبط --</option>
                    <?php foreach($accounts as $acc): ?>
                        <option value="<?php echo $acc->id; ?>" <?php echo $treasury->chart_account_id == $acc->id ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars(($acc->account_code ?? $acc->code) . ' - ' . ($acc->account_name ?? $acc->name)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted mt-1 d-block"><i class="fas fa-info-circle"></i> يتم استخدامه لتوليد القيود المحاسبية التلقائية عند الدفع أو التحصيل.</small>
            </div>
            
        </div>
        <div class="card-footer bg-light d-flex gap-2 mt-0">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/treasury/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>