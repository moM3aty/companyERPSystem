<?php
// app/views/account/create.php
$parents = $data['parents'] ?? [];
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-invoice-dollar text-primary"></i> إضافة حساب مالي جديد</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/account/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group">
                    <label class="form-label">رقم الحساب (الكود) <span class="required">*</span></label>
                    <input type="text" name="code" class="form-control font-monospace" required style="direction:ltr; text-align:right;" placeholder="مثال: 10101">
                </div>

                <div class="form-group">
                    <label class="form-label">اسم الحساب <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="مثال: الصندوق الرئيسي">
                </div>

                <div class="form-group">
                    <label class="form-label">النوع (الطبيعة) <span class="required">*</span></label>
                    <select name="type" class="form-control" required>
                        <option value="asset">أصول (Assets)</option>
                        <option value="liability">خصوم/التزامات (Liabilities)</option>
                        <option value="equity">حقوق الملكية (Equity)</option>
                        <option value="revenue">إيرادات (Revenues)</option>
                        <option value="expense">مصروفات (Expenses)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">الحساب الرئيسي (الأب)</label>
                    <select name="parent_id" class="form-control">
                        <option value="">-- حساب رئيسي مستقل --</option>
                        <?php foreach($parents as $p): ?>
                            <option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->code . ' - ' . $p->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">الرصيد الافتتاحي</label>
                    <input type="number" name="balance" step="0.01" class="form-control font-monospace text-success fw-bold" value="0.00" style="direction:ltr; text-align:right;">
                    <small class="text-muted mt-1 d-block"><i class="fas fa-info-circle"></i> أدخل رصيد الحساب كما هو في بداية المدة المحاسبية (إن وجد).</small>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ الحساب</button>
            <a href="<?php echo URLROOT; ?>/account/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>