<?php
// app/views/treasury/create.php
$accounts = $data['accounts'] ?? [];
?>
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-success-light border-success"><h3 class="card-title text-success-dark mb-0"><i class="fas fa-plus"></i> إضافة صندوق أو حساب بنكي</h3></div>
    <form action="<?php echo URLROOT; ?>/treasury/create" method="POST">
        <div class="card-body form-grid" style="grid-template-columns: 1fr;">
            <div class="form-group"><label class="form-label">الاسم (مثال: بنك الراجحي، صندوق الفرع) <span class="required">*</span></label><input type="text" name="name" class="form-control" required></div>
            <div class="form-group">
                <label class="form-label">النوع (Type)</label>
                <select name="type" class="form-control fw-bold">
                    <option value="Cash">صندوق نقدي (Cash)</option>
                    <option value="Bank">حساب بنكي (Bank Account)</option>
                    <option value="Petty Cash">عهدة نقدية (Petty Cash)</option>
                </select>
            </div>
            <div class="form-group"><label class="form-label">رقم الحساب / الآيبان (للبنوك)</label><input type="text" name="account_number" class="form-control font-monospace" style="direction:ltr; text-align:right;"></div>
            <div class="form-group"><label class="form-label text-success">الرصيد الافتتاحي (Opening Balance)</label><input type="number" step="0.01" name="opening_balance" class="form-control font-monospace fw-bold text-success fs-5 text-center" value="0.00" style="direction:ltr;"></div>
            <div class="form-group">
                <label class="form-label">ربط بدليل الحسابات (شجرة الحسابات)</label>
                <select name="chart_account_id" class="form-control">
                    <option value="">-- اختر الحساب المحاسبي المرتبط --</option>
                    <?php foreach($accounts as $acc): ?><option value="<?php echo $acc->id; ?>"><?php echo htmlspecialchars($acc->account_code . ' - ' . $acc->account_name); ?></option><?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="card-footer bg-light"><button type="submit" class="btn btn-success"><i class="fas fa-save"></i> حفظ</button></div>
    </form>
</div>